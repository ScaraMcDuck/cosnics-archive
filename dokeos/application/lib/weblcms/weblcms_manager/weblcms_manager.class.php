<?php
/**
 * $Id: weblcms.class.php 11676 2007-03-23 14:54:03Z Scara84 $
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/weblcms_component.class.php';
require_once dirname(__FILE__).'/weblcms_search_form.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../category_manager/learning_object_publication_category.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once dirname(__FILE__).'/../tool/tool.class.php';
require_once dirname(__FILE__).'/../tool_list_renderer.class.php';
require_once dirname(__FILE__).'/../course/course.class.php';
require_once dirname(__FILE__).'/../course_group/course_group.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/not_condition.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once dirname(__FILE__).'/component/admin_course_browser/admin_course_browser_table.class.php';
require_once dirname(__FILE__).'/component/subscribed_user_browser/subscribed_user_browser_table.class.php';
require_once dirname(__FILE__).'/component/subscribe_group_browser/subscribe_group_browser_table.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_block.class.php';
require_once dirname(__FILE__).'/../weblcms_rights.class.php';

/**
==============================================================================
 *	This is an application that creates a fully fledged web-based learning
 *	content management system. The Web-LCMS is based on so-called "tools",
 *	which each represent a segment in the application.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class Weblcms extends WebApplication
{
	const APPLICATION_NAME = 'weblcms';

	const PARAM_COURSE = 'course';
	const PARAM_COURSE_GROUP = 'course_group';
	const PARAM_USERS = 'users';
	const PARAM_TOOL = 'tool';
	const PARAM_COMPONENT_ACTION = 'action';
	const PARAM_ACTION = 'go';
	const PARAM_CATEGORY = 'pcattree';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_COURSE_USER_CATEGORY_ID = 'category';
	const PARAM_COURSE_CATEGORY_ID = 'category';
	const PARAM_COURSE_USER = 'course';
	const PARAM_DIRECTION = 'direction';
	const PARAM_REMOVE_SELECTED = 'remove_selected';
	const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';
	const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
	const PARAM_SUBSCRIBE_SELECTED_AS_STUDENT = 'subscribe_selected_as_student';
	const PARAM_SUBSCRIBE_SELECTED_AS_ADMIN = 'subscribe_selected_as_admin';
	const PARAM_SUBSCRIBE_SELECTED_GROUP = 'subscribe_selected_group_admin';
	const PARAM_TOOL_ACTION = 'tool_action';
	const PARAM_STATUS = 'user_status';

	const ACTION_SUBSCRIBE = 'subscribe';
	const ACTION_SUBSCRIBE_GROUPS = 'subscribe_groups';
	const ACTION_UNSUBSCRIBE = 'unsubscribe';
	const ACTION_VIEW_WEBLCMS_HOME = 'home';
	const ACTION_VIEW_COURSE = 'courseviewer';
	const ACTION_CREATE_COURSE = 'coursecreator';
	const ACTION_IMPORT_COURSES = 'courseimporter';
	const ACTION_IMPORT_COURSE_USERS = 'courseuserimporter';
	const ACTION_MANAGER_SORT = 'sort';
	const ACTION_MANAGER_SUBSCRIBE = 'subscribe';
	const ACTION_MANAGER_UNSUBSCRIBE = 'unsubscribe';
	const ACTION_COURSE_CATEGORY_MANAGER = 'catmanager';
	const ACTION_ADMIN_COURSE_BROWSER = 'adminbrowser';
	const ACTION_DELETE_COURSE = 'coursedeleter';
	const ACTION_PUBLISH_INTRODUCTION = 'introduction_publisher';
	const ACTION_DELETE_INTRODUCTION = 'delete_introduction';
	const ACTION_EDIT_INTRODUCTION = 'edit_introduction';
    const ACTION_REPORTING = 'reporting';
	
	const ACTION_RENDER_BLOCK = 'block';

	/**
	 * The tools that this application offers.
	 */
	private $tools;
	/**
	 * The sections that this application offers.
	 */
	private $sections;
	/**
	 * The class of the tool currently active in this application
	 */
	private $tool_class;

	/**
	 * The course object of the course currently active in this application
	 */
	private $course;

	/**
	 * The course_group object of the course_group currently active in this application
	 */
	private $course_group;

	/**
	 * The user object of the currently active user in this application
	 */
	private $user;

	private $search_form;

	/**
	 * Constructor. Optionally takes a default tool; otherwise, it is taken
	 * from the query string.
	 * @param Tool $tool The default tool, or null if none.
	 */
	function Weblcms($user = null, $tool = null)
	{
		parent :: __construct();
		$this->set_parameter(self :: PARAM_ACTION, $_GET[self :: PARAM_ACTION]);
		$this->set_parameter(self :: PARAM_COMPONENT_ACTION, $_GET[self :: PARAM_COMPONENT_ACTION]);
		$this->set_parameter(self :: PARAM_CATEGORY, $_GET[self :: PARAM_CATEGORY]);
		$this->set_parameter(self :: PARAM_COURSE, $_GET[self :: PARAM_COURSE]);
		$this->set_parameter(self :: PARAM_COURSE_GROUP, $_GET[self :: PARAM_COURSE_GROUP]);
		$this->set_parameter(self :: PARAM_TOOL, $_GET[self :: PARAM_TOOL]);

		$this->parse_input_from_table();

		$this->user = $user;
		$this->course = new Course();
		$this->load_course();
		$this->course_group = null;
		$this->load_course_group();
		$this->sections = array ();
		$this->load_sections();
		$this->tools = array ();
		$this->load_tools();
	}

	/*
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;

		switch ($action)
		{
			case self :: ACTION_VIEW_COURSE :
				$component = WeblcmsComponent :: factory('CourseViewer', $this);
				break;
			case self :: ACTION_CREATE_COURSE :
				$component = WeblcmsComponent :: factory('CourseCreator', $this);
				break;
			case self :: ACTION_IMPORT_COURSES :
				$component = WeblcmsComponent :: factory('CourseImporter', $this);
				break;
			case self :: ACTION_IMPORT_COURSE_USERS :
				$component = WeblcmsComponent :: factory('CourseUserImporter', $this);
				break;
			case self :: ACTION_MANAGER_SUBSCRIBE :
				$component = WeblcmsComponent :: factory('Subscribe', $this);
				break;
			case self :: ACTION_MANAGER_UNSUBSCRIBE :
				$component = WeblcmsComponent :: factory('Unsubscribe', $this);
				break;
			case self :: ACTION_SUBSCRIBE_GROUPS :
				$component = WeblcmsComponent :: factory('GroupSubscribe', $this);
				break;
			case self :: ACTION_MANAGER_SORT :
				$component = WeblcmsComponent :: factory('Sorter', $this);
				break;
			case self :: ACTION_COURSE_CATEGORY_MANAGER :
				$component = WeblcmsComponent :: factory('CourseCategoryManager', $this);
				break;
			case self :: ACTION_ADMIN_COURSE_BROWSER :
				$component = WeblcmsComponent :: factory('AdminCourseBrowser', $this);
				break;
			case self :: ACTION_DELETE_COURSE :
				$component = WeblcmsComponent :: factory('CourseDeleter', $this);
				break;
			case self :: ACTION_PUBLISH_INTRODUCTION:
				$component = WeblcmsComponent :: factory('IntroductionPublisher', $this);
				break;
			case self :: ACTION_DELETE_INTRODUCTION:
				$component = WeblcmsComponent :: factory('IntroductionDeleter', $this);
				break;
			case self :: ACTION_EDIT_INTRODUCTION:
				$component = WeblcmsComponent :: factory('IntroductionEditor', $this);
				break;
            case self :: ACTION_REPORTING:
                $component = WeblcmsComponent :: factory('Reporting', $this);
                break;
			default :
				$this->set_action(self :: ACTION_VIEW_WEBLCMS_HOME);
				$component = WeblcmsComponent :: factory('Home', $this);
		}
		$component->run();
	}
	
    /**
	 * Renders the weblcms block and returns it. 
	 */
	function render_block($block)
	{
		$weblcms_block = WeblcmsBlock :: factory($this, $block);
		return $weblcms_block->run();
	}

	/**
	 * Gets the current action.
	 * @see get_parameter()
	 * @return string The current action.
	 */
	function get_action()
	{
		return $this->get_parameter(self :: PARAM_ACTION);
	}

	/**
	 * Sets the current action.
	 * @param string $action The new action.
	 */
	function set_action($action)
	{
		return $this->set_parameter(self :: PARAM_ACTION, $action);
	}

	function set_tool_class($class)
	{
		return $this->tool_class = $class;
	}

	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		if ($action == self :: ACTION_VIEW_WEBLCMS_HOME)
		{
			$this->set_parameter(self :: PARAM_TOOL, null);
			$action = null;
		}
		return parent :: redirect($action, $message, $error_message, $extra_params);
	}

	/**
	 * Gets the identifier of the current tool
	 * @return string The identifier of current tool
	 */
	function get_tool_id()
	{
		return $this->get_parameter(self :: PARAM_TOOL);
	}

	/**
	 * Gets the user id.
	 * @return int The user id.
	 */
	function get_user_id()
	{
		if ($this->user == null)
			return 0;
			
		return $this->user->get_id();
	}

	/**
	 * Gets the user.
	 * @return int The user.
	 */
	function get_user()
	{
		return $this->user;
	}

	/**
	 * Gets the user object for a given user
	 * @param int $user_id
	 * @return User
	 */
	function get_user_info($user_id)
	{
		return UserDataManager :: get_instance()->retrieve_user($user_id);
	}

	/**
	 * Returns the course that is being used.
	 * @return string The course.
	 */
	function get_course()
	{
		return $this->course;
	}
	/**
	 * Sets the course
	 * @param Course $course
	 */
	function set_course($course)
	{
		$this->course = $course;
	}
	/**
	 * Returns the identifier of the course that is being used.
	 * @return string The course identifier.
	 */
	function get_course_id()
	{
		if ($this->course == null)
			return 0;
			
		return $this->course->get_id();
	}
	/**
	 * Returns the course_group that is being used.
	 * @return string The course_group.
	 */
	function get_course_group()
	{
		return $this->course_group;
	}
	/**
	 * Sets the course_group
	 * @param CourseGroup $course_group
	 */
	function set_course_group($course_group)
	{
		$this->course_group = $course_group;
	}
	/**
	 * Gets a list of all course_groups of the current active course in which the
	 * current user is subscribed.
	 */
	function get_course_groups()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$course_groups = $wdm->retrieve_course_groups_from_user($this->get_user(),$this->get_course())->as_array();
		return $course_groups;
	}
	/**
	 * Gets the defined categories in the current tool.
	 * @param boolean $list When true the categories will be returned as a list.
	 * If false (default value) a tree structure of the categories will be
	 * returned
	 * @return array The categories
	 */
//	function get_categories($list = false)
//	{
//		return ($list ? $this->get_category_list() : $this->get_category_tree());
//	}
//	/**
//	 * Gets the defined categories in the current tool structured as a tree.
//	 * @return array
//	 */
//	private function get_category_tree()
//	{
//		/*
//		 * Add the root category.
//		 */
//		$course = $this->get_course_id();
//		$tool = $this->get_parameter(self :: PARAM_TOOL);
//	//	$cats = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($course, $tool);
//		$root = array ();
//		//$root['obj'] = new LearningObjectPublicationCategory(0, Translation :: get('RootCategory'), $course->get_code, $tool, 0);
//		//$root['sub'] = $cats;
//		$tree = array ();
//		//$tree[] = $root;
//		return $tree;
//	}
//	/**
//	 * Gets a list of the defined categories in the current tool.
//	 */
//	private function get_category_list()
//	{
//		$categories = array ();
//		$tree = $this->get_category_tree();
//		self :: translate_category_tree($tree, $categories);
//		return $categories;
//	}
	/**
	 * Makes a category tree ready for displaying by adding a prefix to the
	 * category title based on the level of that category in the tree structure.
	 * @param array $tree The category tree
	 * @param array $categories In this array the new category titles (with
	 * prefix) will be stored. The keys in this array are the category ids, the
	 * values are the new titles
	 * @param int $level The current level in the tree structure
	 */
	private static function translate_category_tree($tree, $categories, $level = 0)
	{
		foreach ($tree as $node)
		{
			$obj = $node['obj'];
			$prefix = ($level ? str_repeat('&nbsp;&nbsp;&nbsp;', $level).'&mdash; ' : '');
			$categories[$obj->get_id()] = $prefix.$obj->get_title();
			$subtree = $node['sub'];
			if (is_array($subtree) && count($subtree))
			{
				self :: translate_category_tree($subtree, $categories, $level +1);
			}
		}
	}
	/**
	 * Gets a category
	 * @param int $id The id of the requested category
	 * @return LearningPublicationCategory The requested category
	 */
	function get_category($id)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_category($id);
	}
	/**
	 * Displays the header of this application
	 * @param array $breadcrumbs The breadcrumbs which should be displayed
	 */
	function display_header($breadcrumbtrail, $display_search = false, $display_title = true)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}
		
		$tool = $this->get_parameter(self :: PARAM_TOOL);
		$course = $this->get_parameter(self :: PARAM_COURSE);
		$action = $this->get_parameter(self :: PARAM_ACTION);
		
		if (isset ($this->tool_class))
		{
			$tool = str_replace('_tool', '', Tool :: class_to_type($this->tool_class));
			$js_file = dirname(__FILE__).'/tool/'.$tool.'/'.$tool.'.js';
			if (file_exists($js_file))
			{
				$htmlHeadXtra[] = '<script type="text/javascript" src="application/lib/weblcms/tool/'.$tool.'/'.$tool.'.js"></script>';
			}
		}
		
		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: header($breadcrumbtrail);
		
		if (isset ($this->tool_class))
		{
			/*echo '<div style="float: right; margin: 0 0 0.5em 0.5em; padding: 0.5em; border: 1px solid #DDD; background: #FAFAFA;">';
			echo '<form method="get" action="'.$this->get_url().'" style="display: inline;">';
			echo '<input type="hidden" name="'.self :: PARAM_ACTION.'" value="courseviewer" />';
			echo '<input type="hidden" name="'.self :: PARAM_COURSE.'" value="'. $this->get_course_id() .'" />';
			echo '<input type="hidden" name="'.WebApplication :: PARAM_APPLICATION.'" value="'.$this->get_parameter('application').'"/>';
			echo '<select name="'.self :: PARAM_TOOL.'" onchange="submit();">';
			$tools = array ();
			foreach ($this->get_registered_tools() as $t)
			{
				$tools[$t->name]['title']	= htmlentities(Translation :: get(Tool :: type_to_class($t->name).'Title'));
				$tools[$t->name]['visible']	= $t->visible;
				$tools[$t->name]['section']	= $t->section;
			}
			asort($tools);
			foreach ($tools as $tool => $properties)
			{
				if (($properties['visible'] && $properties['section'] != 'course_admin') || $this->get_course()->is_course_admin($this->get_user()))
				{
					$class = Tool :: type_to_class($tool);
					echo '<option value="'.$tool.'"'. ($class == $this->tool_class ? ' selected="selected"' : '').'>'.htmlentities($properties['title']).'</option>';
				}
			}
			echo '</select></form></div>';*/
            if($display_title)
            {
               echo '<div style="float: left;">';
                Display :: tool_title(htmlentities(Translation :: get($this->tool_class.'Title')));
                echo '</div>';
            }
			
		}
		else
		{
			if ($course && is_object($this->course) && $action == self :: ACTION_VIEW_COURSE)
			{
				//echo '<h3 style="float: left;">'.htmlentities($this->course->get_name()).'</h3>';
				echo '<h3 style="float: left;">'.htmlentities($title).'</h3>';
				// TODO: Add department name and url here somewhere ?
			}
			else
			{
				echo '<h3 style="float: left;">'.htmlentities($title).'</h3>';
				if ($display_search)
				{
					$this->display_search_form();
				}
			}

			//echo '<div class="clear">&nbsp;</div>';
		}
		
		if (!isset ($this->tool_class))
		{
			if ($msg = $_GET[self :: PARAM_MESSAGE])
			{
				echo '<br />';
				$this->display_message($msg);
			}
			if($msg = $_GET[self::PARAM_ERROR_MESSAGE])
			{
				echo '<br />';
				$this->display_error_message($msg);
			}
		}
		//echo 'Last visit: '.date('r',$this->get_last_visit_date());
	}

	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: warning_message($message);
	}

	/**
	 * Displays the footer of this application
	 */
	function display_footer()
	{
		Display :: footer();
	}
	
	/**
	 * Displays an error page.
	 * @param string $message The message.
	 */
	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}

	/**
	 * Returns the names of the tools known to this application.
	 * @return array The tools.
	 */
	function get_registered_tools()
	{
		return $this->tools;
	}
	
	/**
	 * Returns the names of the sections known to this application.
	 * @return array The tools.
	 */
	function get_registered_sections()
	{
		return $this->sections;
	}
	
	function get_tool_properties($tool)
	{
		return $this->tools[$tool];
	}

	/**
	 * Loads the tools installed on the system.
	 */
	function load_tools()
	{
		if(!is_null($this->get_course_id()))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->tools = $wdm->get_course_modules($this->get_course_id());
			
			foreach($this->tools as $index => $tool)
			{
				require_once dirname(__FILE__).'/../tool/'.$tool->name.'/'.$tool->name.'_tool.class.php';
			}
		}
	}
	
	/**
	 * Loads the sections installed on the system.
	 */
	function load_sections()
	{
		if(!is_null($this->get_course_id()))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->sections = $wdm->get_course_sections($this->get_course_id());
		}
	}

	/**
	 * Loads the current course into the system.
	 */
	private function load_course()
	{
		if(!is_null($this->get_parameter(self :: PARAM_COURSE)))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->course = $wdm->retrieve_course($this->get_parameter(self :: PARAM_COURSE));
		}
	}
	/**
	 * Loads the current course_group into the system.
	 */
	private function load_course_group()
	{
		if(!is_null($this->get_parameter(self :: PARAM_COURSE_GROUP)) && strlen($this->get_parameter(self :: PARAM_COURSE_GROUP)>0))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->course_group = $wdm->retrieve_course_group($this->get_parameter(self :: PARAM_COURSE_GROUP));
		}
	}

	/**
	 * Determines whether or not the given name is a valid tool name.
	 * @param string $name The name to evaluate.
	 * @return True if the name is a valid tool name, false otherwise.
	 */
	static function is_tool_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}
	/*
	 * Inherited
	 */
	function retrieve_max_sort_value($table, $column, $condition = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_max_sort_value($table, $column, $condition);
	}

	/*
	 * Inherited
	 */
	function learning_object_is_published($object_id)
	{
		return WeblcmsDataManager :: get_instance()->learning_object_is_published($object_id);
	}
	/*
	 * Inherited
	 */
	function any_learning_object_is_published($object_ids)
	{
		return WeblcmsDataManager :: get_instance()->any_learning_object_is_published($object_ids);
	}
	/*
	 * Inherited
	 */
	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return WeblcmsDataManager :: get_instance()->get_learning_object_publication_attributes($this->get_user(), $object_id, $type, $offset, $count, $order_property, $order_direction);
	}

	/*
	 * Inherited
	 */
	function get_learning_object_publication_attribute($publication_id)
	{
		return WeblcmsDataManager :: get_instance()->get_learning_object_publication_attribute($publication_id);
	}

	/*
	 * Inherited
	 */
	function delete_learning_object_publications($object_id)
	{
		return WeblcmsDataManager :: get_instance()->delete_learning_object_publications($object_id);
	}

	/*
	 * Inherited
	 */
	function update_learning_object_publication_id($publication_attr)
	{
		return WeblcmsDataManager :: get_instance()->update_learning_object_publication_id($publication_attr);
	}

	/**
	 * Inherited
	 */
	function count_publication_attributes($type = null, $condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_publication_attributes($this->get_user(), $type, $condition);
	}
	
	/**
	 * Inherited
	 */
	function get_learning_object_publication_locations($learning_object)
	{
		$locations = array();
		
		$type = $learning_object->get_type();
		
		$courses = $this->retrieve_courses($user); 
		while($course = $courses->next_result())
			$c[] = $course;
		
		$directory = dirname(__FILE__) . '/../tool/';
		$tools = Filesystem :: get_directory_content($directory, Filesystem::LIST_DIRECTORIES, false);
		foreach($tools as $tool)
		{
			$path =  $directory . $tool . '/' . $tool . '_tool.class.php';
			
			if(!file_exists($path)) continue;
			
			require_once $path;
			$class = DokeosUtilities :: underscores_to_camelcase($tool) . 'Tool';
			$obj = new $class($this);
			$types[$tool] = $obj->get_allowed_types();
		}
		
		foreach($types as $tool => $allowed_types)
		{
			if(in_array($type, $allowed_types))
			{
				$user = Session :: get_user_id();
				
				foreach($c as $course)
					$locations[$course->get_id() . '-' . $tool] = 'Course: ' . $course->get_name() . ' - Tool: ' . $tool;
			}
		}
		
		return $locations;
	}
	
	function publish_learning_object($learning_object, $location)
	{
		$location_split = split('-', $location);
		$course = $location_split[0];
		$tool = $location_split[1]; //echo $location;
		$dm = WeblcmsDataManager :: get_instance();
		$do = $dm->get_next_learning_object_publication_display_order_index($course,$tool,0);
		
		$pub = new LearningObjectPublication(null, $learning_object, $course, $tool, 0, array(), array(), 0, 0, Session :: get_user_id(), time(), time(), 0, $do, false, 0);
		$pub->create();
		
		return Translation :: get('PublicationCreated') . ': <b>' . Translation :: get('Course') . '</b>: ' . $course .
			   ' - <b>' . Translation :: get('Tool') . '</b>: ' . $tool;
	}

	/**
	 * Count the number of courses
	 * @param Condition $condition
	 * @return int
	 */
	function count_courses($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_courses($condition);
	}

	/**
	 * Count the number of course categories
	 * @param Condition $condition
	 * @return int
	 */
	function count_course_categories($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_course_categories($condition);
	}

	/**
	 * Count the number of courses th user is subscribed to
	 * @param Condition $condition
	 * @return int
	 */
	function count_user_courses($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_user_courses($condition);
	}

	/**
	 * Count the number of course user categories
	 * @param Condition $condition
	 * @return int
	 */
	function count_course_user_categories($condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_course_user_categories($condition);
	}

	/**
	 * Retrieves the course categories that match the criteria from persistent storage.
	 * @param string $parent The parent of the course category.
	 * @return DatabaseCourseCategoryResultSet The resultset of course category.
	 */
	function retrieve_course_categories($conditions = null, $offset = null, $count = null, $orderBy = null, $orderDir = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_categories($conditions, $offset, $count, $orderBy, $orderDir);
	}

	/**
	 * Retrieves the personal course categories for a given user.
	 * @return DatabaseUserCourseCategoryResultSet The resultset of course categories.
	 */
	function retrieve_course_user_categories($conditions = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_categories($conditions, $offset, $count, $order_property, $order_direction);
	}

	/**
	 * Retrieves a personal course category for the user.
	 * @return CourseUserCategory The course user category.
	 */
	function retrieve_course_user_category($course_user_category_id, $user_id = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_category($course_user_category_id, $user_id);
	}

	/**
	 * Retrieves a personal course category for the user according to
	 * @param int $user_id
	 * @param int $sort
	 * @param string $direction
	 * @return CourseUserCategory The course user category.
	 */
	function retrieve_course_user_category_at_sort($user_id, $sort, $direction)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_category_at_sort($user_id, $sort, $direction);
	}

	/**
	 * Retrieves a single course from persistent storage.
	 * @param string $course_code The alphanumerical identifier of the course.
	 * @return Course The course.
	 */
	function retrieve_course($course_code)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course($course_code);
	}

	/**
	 * Retrieves a single course category from persistent storage.
	 * @param string $category_code The alphanumerical identifier of the course category.
	 * @return CourseCategory The course category.
	 */
	function retrieve_course_category($course_category)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_category($course_category);
	}

	/**
	 * Retrieves a single course user relation from persistent storage.
	 * @param string $course_code
	 * @param int $user_id
	 * @return CourseCategory The course category.
	 */
	function retrieve_course_user_relation($course_code, $user_id)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_relation($course_code, $user_id);
	}

	/**
	 * Retrieves the next course user relation according to.
	 * @param int $user_id
	 * @param int $category_id
	 * @param int $sort
	 * @param string $direction
	 * @return CourseUserRelationResultSet
	 */
	function retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction);
	}

	/**
	 * Retrieves a set of course user relations
	 * @param int $user_id
	 * @param string $course_user_category
	 */
	function retrieve_course_user_relations($user_id, $course_user_category)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_relations($user_id, $course_user_category);
	}

	/**
	 * Retrieves the relations for the users subscribed to a certain course.
	 * @param Course $course
	 * @return CourseUserRelationResultSet
	 */
	function retrieve_course_users($course)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_course_users($course);
	}

	/**
	 * Retrieve a series of courses
	 * @param User $user
	 * @param string $category
	 * @param Condition $condition
	 * @param array $orderBy
	 * @param array $orderDir
	 * @param int $offset
	 * @param int $maxObjects
	 * @return CourseResultSet
	 */
	function retrieve_courses($user = null, $condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_courses($user, $condition, $offset, $count, $order_property, $order_direction);
	}

	/**
	 * Retrieve a series of courses for a specific user + the relation
	 * @param Condition $condition
	 * @param array $orderBy
	 * @param array $orderDir
	 * @param int $offset
	 * @param int $maxObjects
	 * @return CourseResultSet
	 */
	function retrieve_user_courses($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_user_courses($condition, $offset, $count, $order_property, $order_direction);
	}

	/**
	 * Gets the date of the last visit of current user to the current location
	 * @param string $tool If $tool equals null, current active tool will be
	 * taken into account. If no tool is given or no tool is active the date of
	 * last visit to the course homepage will be returned.
	 * @param int $category_id The category in the given tool of which the last
	 * visit date is requested. If $category_id equals null, the current active
	 * category will be used.
	 * @return int
	 */
	function get_last_visit_date($tool = null,$category_id = null)
	{
		if(is_null($tool))
		{
			$tool = $this->get_parameter(self :: PARAM_TOOL);
		}
		if(is_null($category_id))
		{
			$category_id = $this->get_parameter(self :: PARAM_CATEGORY);
			if(is_null($category_id))
			{
				$category_id = 0;
			}
		}
		$wdm = WeblcmsDataManager :: get_instance();
		$date = $wdm->get_last_visit_date($this->get_course_id(),$this->get_user_id(),$tool,$category_id);
		return $date;
	}
	/**
	 * Determines if a tool has new publications  since the last time the
	 * current user visited the tool.
	 * @todo This function now uses the count_learning_object_publications
	 * function and for each tool a query is executed. All information can be
	 * retrieved using a single query. WeblcmsDataManager should implement this
	 * functionality.
	 * @todo This function currently doesn't take the course_group information into
	 * account. So it's possible this function returns true even if there's no
	 * new publication for the current user
	 * @param string $tool
	 */
	function tool_has_new_publications($tool)
	{
		$class = Tool :: type_to_class($tool);
		$tool_object = new $class ($this);
		if(is_subclass_of($tool_object,'Tool'))
		{
			$last_visit_date = $this->get_last_visit_date($tool);
			$wdm = WeblcmsDataManager :: get_instance();
			$conditions = array();
			$conditions[] = new EqualityCondition('tool',$tool);
			$conditions[] = new InequalityCondition('modified',InequalityCondition::GREATER_THAN,$last_visit_date);
			if (!$this->get_course()->is_course_admin($this->get_user()) && !$this->user->is_platform_admin())
			{
				// Only select visible publications
				$conditions[] = new EqualityCondition('hidden',0);
				// Only select publications which are published forever OR
				// of which the current time is in the publication period and the last visit date is before the from_date.
				$conditions_publication_period = array();
				$conditions_publication_period[] = new InequalityCondition('from_date',InequalityCondition::LESS_THAN_OR_EQUAL,time());
				$conditions_publication_period[] = new InequalityCondition('to_date',InequalityCondition::GREATER_THAN_OR_EQUAL,time());
				$conditions_publication_period[] = new InequalityCondition('from_date',InequalityCondition::GREATER_THAN_OR_EQUAL,$last_visit_date);
				$condition_publication_period = new AndCondition($conditions_publication_period);
				$condition_publication_forever = new EqualityCondition('from_date',0);
				$conditions[] = new OrCondition($condition_publication_forever,$condition_publication_period);
			}
			$course_groups = $wdm->retrieve_course_groups_from_user($this->get_user(),$this->get_course())->as_array();
			$condition = new AndCondition($conditions);
			$new_items = $wdm->count_learning_object_publications($this->get_course_id(),null,$this->get_user_id(),$course_groups,$condition);
			return $new_items > 0;
		}
		return false;
	}

    /**
     * Returns the url to the course's page
     * @param Course $course
     * @return String
     */
	function get_course_viewing_url($course)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id()));
	}
	
    /**
     * Returns the link to the course's page
     * @param Course $course
     * @return String
     */
	function get_course_viewing_link($course, $encode = false)
	{
		return $this->get_link(array (self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id()), $encode);
	}

    /**
     * Returns the editing url for the course
     * @param Course $course
     * @return String
     */
	function get_course_editing_url($course)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id(), self :: PARAM_TOOL => 'course_settings', 'previous' => 'admin'));
	}

    /**
     * Returns the maintenance url for the course
     * @param Course $course
     * @return String
     */
	function get_course_maintenance_url($course)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE, self :: PARAM_COURSE => $course->get_id(), self :: PARAM_TOOL => 'maintenance'));
	}

    /**
     * Returns the subscription url for the course
     * @param Course $course
     * @return String
     */
	function get_course_subscription_url($course)
	{
		if (!$this->course_subscription_allowed($course))
		{
			return null;
		}

		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_SUBSCRIBE ,self :: PARAM_COURSE => $course->get_id()));
	}

    /**
     * Returns the unsubscription url for the course
     * @param Course $course
     * @return String
     */
	function get_course_unsubscription_url($course)
	{
		if (!$this->course_unsubscription_allowed($course))
		{
			return null;
		}

		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_UNSUBSCRIBE ,self :: PARAM_COURSE => $course->get_id()));
	}

    /**
     * Returns the editing url for the course user category
     * @param CourseUsercategory $course_user_category
     * @return String
     */
	function get_course_user_category_edit_url($course_user_category)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT , self :: PARAM_COMPONENT_ACTION => 'edit', self :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category->get_id()));
	}

    /**
     * Returns the creating url for a course user category
     * @return String
     */
	function get_course_user_category_add_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT , self :: PARAM_COMPONENT_ACTION => 'add'));
	}

    /**
     * Returns the moving url for the course user category
     * @param CourseUserCategory $course_user_category
     * @param string $direction
     * @return String
     */
	function get_course_user_category_move_url($course_user_category, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT , self :: PARAM_COMPONENT_ACTION => 'movecat', self :: PARAM_DIRECTION => $direction, self :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category->get_id()));
	}

    /**
     * Returns the deleting url for the course user category
     * @param CourseUserCategory $course_user_category
     * @return String
     */
	function get_course_user_category_delete_url($course_user_category)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT , self :: PARAM_COMPONENT_ACTION => 'delete', self :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category->get_id()));
	}

    /**
     * Returns the editing url for the course category
     * @param CourseCategory $course_category
     * @return String
     */
	function get_course_category_edit_url($coursecategory)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER , self :: PARAM_COMPONENT_ACTION => 'edit', self :: PARAM_COURSE_CATEGORY_ID => $coursecategory->get_code()));
	}

    /**
     * Returns the creating url for a course category
     * @return String
     */
	function get_course_category_add_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER , self :: PARAM_COMPONENT_ACTION => 'add'));
	}

    /**
     * Returns the deleting url for the course category
     * @param CourseCategory $course_category
     * @return String
     */
	function get_course_category_delete_url($coursecategory)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER , self :: PARAM_COMPONENT_ACTION => 'delete', self :: PARAM_COURSE_CATEGORY_ID => $coursecategory->get_code()));
	}

    /**
     * Returns the editing url for the course category
     * @param CourseCategory $course_category
     * @return String
     */
	function get_course_user_edit_url($course_user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT , self :: PARAM_COMPONENT_ACTION => 'assign', self :: PARAM_COURSE_USER => $course_user->get_id()));
	}

    /**
     * Returns the moving url for the course user relation
     * @param CourseUserRelation $course_user
     * @param string $direction
     * @return String
     */
	function get_course_user_move_url($course_user, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGER_SORT , self :: PARAM_COMPONENT_ACTION => 'move', self :: PARAM_DIRECTION => $direction, self :: PARAM_COURSE_USER => $course_user->get_id()));
	}

    /**
     * Checks whether subscription to the course is allowed for the current user
     * @param Course $course
     * @return boolean
     */
	function course_subscription_allowed($course)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->course_subscription_allowed($course, $this->get_user_id());
	}

    /**
     * Checks whether unsubscription from the course is allowed for the current user
     * @param Course $course
     * @return boolean
     */
	function course_unsubscription_allowed($course)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->course_unsubscription_allowed($course, $this->get_user());
	}

    /**
     * Checks whether the user is subscribed to the given course
     * @param Course $course
     * @param int $user_id
     * @return boolean
     */
	function is_subscribed($course, $user_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->is_subscribed($course, $user_id);
	}

	/**
	 * Subscribe a user to a course.
	 * @param Course $course
	 * @param int $status
	 * @param int $tutor_id
	 * @param int $user_id
	 * @return boolean
	 */
	function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->subscribe_user_to_course($course, $status, $tutor_id, $user_id);
	}

	/**
	 * Unsubscribe a user from a course.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	function unsubscribe_user_from_course($course, $user_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->unsubscribe_user_from_course($course, $user_id);
	}

	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}

	/**
	 * @todo Clean this up. It's all SortableTable's fault. :-(
	 */
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_course_ids = $_POST[AdminCourseBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty ($selected_course_ids))
			{
				$selected_course_ids = array ();
			}
			elseif (!is_array($selected_course_ids))
			{
				$selected_course_ids = array ($selected_course_ids);
			}

			$selected_user_ids = $_POST[SubscribedUserBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty ($selected_user_ids))
			{
				$selected_user_ids = array ();
			}
			elseif (!is_array($selected_user_ids))
			{
				$selected_user_ids = array ($selected_user_ids);
			}
			
			$selected_group_ids = $_POST[SubscribeGroupBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty ($selected_group_ids))
			{
				$selected_group_ids = array ();
			}
			elseif (!is_array($selected_group_ids))
			{
				$selected_group_ids = array ($selected_group_ids);
			}

			switch ($_POST['action'])
			{
				case self :: PARAM_REMOVE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_COURSE);
					$_GET[self :: PARAM_COURSE] = $selected_course_ids;
					break;

				case self :: PARAM_UNSUBSCRIBE_SELECTED :
					$this->set_action(self :: ACTION_MANAGER_UNSUBSCRIBE);
					$_GET[self :: PARAM_USERS] = $selected_user_ids;
					break;

				case self :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT :
					$this->set_action(self :: ACTION_MANAGER_SUBSCRIBE);
					$_GET[self :: PARAM_USERS] = $selected_user_ids;
					$_GET[self :: PARAM_STATUS] = 5;
					break;

				case self :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN :
					$this->set_action(self :: ACTION_MANAGER_SUBSCRIBE);
					$_GET[self :: PARAM_USERS] = $selected_user_ids;
					$_GET[self :: PARAM_STATUS] = 1;
					break;
				case self :: PARAM_SUBSCRIBE_SELECTED_GROUP :
					$this->set_action(self :: ACTION_SUBSCRIBE_GROUPS);
					$_GET['group_id'] = $selected_group_ids;
					$_GET[self :: PARAM_STATUS] = 1;
					break;
			}
		}
	}

	/**
	 * Gets the search form.
	 * @return RepositorySearchForm The search form.
	 */
	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new WeblcmsSearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}

	/**
	 * Gets the search condition
	 * @return Condition
	 */
	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}

	/**
	 * Returns whether the search form has validated
	 * @return boolean
	 */
	function get_search_validate()
	{
		return $this->get_search_form()->validate();
	}

	/**
	 * Gets the search parameter
	 * @param string $name
	 * @return string
	 */
	function get_search_parameter($name)
	{
		return $this->search_parameters[$name];
	}

	/**
	 * Displays the search form
	 */
	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}

	/**
	 * Returns a list of actions available to the admin.
	 * @param User $user The current user.
	 * @return Array $info Contains all possible actions.
	 */
	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('List'),
							'description' => Translation :: get('ListDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_ADMIN_COURSE_BROWSER)));
		$links[]	= array('name' => Translation :: get('Create'),
							'description' => Translation :: get('CreateDescription'),
							'action' => 'add',
							'url' => $this->get_link(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_CREATE_COURSE)));
		$links[]	= array('name' => Translation :: get('Import'),
							'description' => Translation :: get('ImportDescription'),
							'action' => 'import',
							'url' => $this->get_link(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_IMPORT_COURSES)));
		$links[]	= array('name' => Translation :: get('CourseCategoryManagement'),
							'description' => Translation :: get('CourseCategoryManagementDescription'),
							'action' => 'category',
							'url' => $this->get_link(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_COURSE_CATEGORY_MANAGER)));
		$links[]	= array('name' => Translation :: get('UserImport'),
							'description' => Translation :: get('UserImportDescription'),
							'action' => 'import',
							'url' => $this->get_link(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_IMPORT_COURSE_USERS)));
		return array('application' => array('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links, 'search' => $this->get_link(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_ADMIN_COURSE_BROWSER)));
	}

	/**
	 * Return a link to a certain action of this application
	 * @param array $paramaters The parameters to be added to the url
	 * @param boolean $encode Should the url be encoded ?
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'run.php';
		$parameters['application'] = self :: APPLICATION_NAME;
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		return PlatformSetting :: get($variable, $application);
	}
	
	function get_parent()
	{
		return $this;	
	}

    function get_reporting_url($classname, $params)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, ReportingManager::PARAM_TEMPLATE_NAME => $classname, ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params));
    }
}
?>