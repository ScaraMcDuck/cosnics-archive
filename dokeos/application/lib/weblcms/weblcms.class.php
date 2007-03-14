<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../webapplication.class.php';
require_once dirname(__FILE__).'/weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/learningobjectpublicationcategory.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/groupmanager.lib.php';
require_once dirname(__FILE__).'/tool/tool.class.php';
require_once dirname(__FILE__).'/toollistrenderer.class.php';

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
	const PARAM_TOOL = 'tool';
	const PARAM_ACTION = 'weblcms_action';
	const PARAM_CATEGORY = 'pcattree';

	/**
	 * The tools that this application offers.
	 */
	private $tools;
	/**
	 * The class of the tool currently active in this application
	 */
	private $tool_class;

	/**
	 * Constructor. Optionally takes a default tool; otherwise, it is taken
	 * from the query string.
	 * @param Tool $tool The default tool, or null if none.
	 */
	function Weblcms($tool = null)
	{
		parent :: __construct();
		$this->set_parameter(self :: PARAM_TOOL, $_GET[self :: PARAM_TOOL]);
		$this->set_parameter(self :: PARAM_ACTION, $_GET[self :: PARAM_ACTION]);
		$this->set_parameter(self :: PARAM_CATEGORY, $_GET[self :: PARAM_CATEGORY]);
		$this->tools = array ();
		$this->load_tools();
	}

	/*
	 * Inherited.
	 */
	function run()
	{
		$tool = $this->get_parameter(self :: PARAM_TOOL);
		$action = $this->get_parameter(self::PARAM_ACTION);
		$category = $this->get_parameter(self::PARAM_CATEGORY);
		if(is_null($category))
		{
			$category = 0;
		}
		if($action)
		{
			$wdm = WeblcmsDataManager :: get_instance();
			switch($action)
			{
				case 'make_visible':
					$wdm->set_module_visible($this->get_course_id(),$tool,true);
					$this->load_tools();
					break;
				case 'make_invisible':
					$wdm->set_module_visible($this->get_course_id(),$tool,false);
					$this->load_tools();
					break;
			}
			$this->set_parameter(self :: PARAM_TOOL, null);
		}
		if ($tool && !$action)
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$class = Tool :: type_to_class($tool);
			$toolObj = new $class ($this);
			$this->tool_class = $class;
			$toolObj->run();
			$wdm->log_course_module_access($this->get_course_id(),$this->get_user_id(),$tool,$category);
		}
		else
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->display_header();
			$renderer = ToolListRenderer::factory('FixedLocationToolListRenderer',$this);
			$renderer->display();
			$this->display_footer();
			$wdm->log_course_module_access($this->get_course_id(),$this->get_user_id(),null);
		}
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
	 * Returns the numeric identifier of the active user.
	 * @return string The user identifier.
	 */
	function get_user_id()
	{
		return api_get_user_id();
	}

	/**
	 * Returns the identifier of the course that is being used.
	 * @return string The course identifier.
	 */
	function get_course_id()
	{
		return api_get_course_id();
	}
	/**
	 * Gets a list of all groups of the current active course in which the
	 * current user is subscribed.
	 */
	function get_groups()
	{
		return GroupManager :: get_group_ids($this->get_course_id(), $this->get_user_id());
	}
	/**
	 * Gets the defined categories in the current tool.
	 * @param boolean $list When true the categories will be returned as a list.
	 * If false (default value) a tree structure of the categories will be
	 * returned
	 * @return array The categories
	 */
	function get_categories($list = false)
	{
		return ($list ? $this->get_category_list() : $this->get_category_tree());
	}
	/**
	 * Gets the defined categories in the current tool structured as a tree.
	 * @return array
	 */
	private function get_category_tree()
	{
		/*
		 * Add the root category.
		 */
		$course = $this->get_course_id();
		$tool = $this->get_parameter(self :: PARAM_TOOL);
		$cats = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($course, $tool);
		$root = array ();
		$root['obj'] = & new LearningObjectPublicationCategory(0, get_lang('RootCategory'), $course, $tool, 0);
		$root['sub'] = & $cats;
		$tree = array ();
		$tree[] = & $root;
		return $tree;
	}
	/**
	 * Gets a list of the defined categories in the current tool.
	 */
	private function get_category_list()
	{
		$categories = array ();
		$tree = $this->get_category_tree();
		self :: translate_category_tree(& $tree, & $categories);
		return $categories;
	}
	/**
	 * Makes a category tree ready for displaying by adding a prefix to the
	 * category title based on the level of that category in the tree structure.
	 * @param array $tree The category tree
	 * @param array $categories In this array the new category titles (with
	 * prefix) will be stored. The keys in this array are the category ids, the
	 * values are the new titles
	 * @param int $level The current level in the tree structure
	 */
	private static function translate_category_tree(& $tree, & $categories, $level = 0)
	{
		foreach ($tree as $node)
		{
			$obj = $node['obj'];
			$prefix = ($level ? str_repeat('&nbsp;&nbsp;&nbsp;', $level).'&mdash; ' : '');
			$categories[$obj->get_id()] = $prefix.$obj->get_title();
			$subtree = $node['sub'];
			if (is_array($subtree) && count($subtree))
			{
				self :: translate_category_tree(& $subtree, & $categories, $level +1);
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
	function display_header($breadcrumbs = array ())
	{
		global $interbredcrump, $htmlHeadXtra;
		$tool = $this->get_parameter(self :: PARAM_TOOL);
		if ($tool)
		{
			array_unshift($breadcrumbs, array ('url' => $this->get_url(), 'name' => get_lang(Tool :: type_to_class($tool).'Title')));
		}
		$current_crumb = array_pop($breadcrumbs);
		$interbredcrump = $breadcrumbs;
		if (isset ($this->tool_class))
		{
			$tool = str_replace('_tool', '', Tool :: class_to_type($this->tool_class));
			$js_file = dirname(__FILE__).'/tool/'.$tool.'/'.$tool.'.js';
			if (file_exists($js_file))
			{
				$htmlHeadXtra[] = '<script type="text/javascript" src="application/lib/weblcms/tool/'.$tool.'/'.$tool.'.js"></script>';
			}
		}
		$title = $current_crumb['name'];
		Display :: display_header($title);
		if (isset ($this->tool_class))
		{
			echo '<div style="float: right; margin: 0 0 0.5em 0.5em; padding: 0.5em; border: 1px solid #DDD; background: #FAFAFA;">';
			echo '<form method="get" action="'.$this->get_url().'" style="display: inline;">';
			echo '<select name="'.self :: PARAM_TOOL.'" onchange="submit();">';
			$tools = array ();
			foreach ($this->get_registered_tools() as $t)
			{
				$tools[$t->name] = htmlentities(get_lang(Tool :: type_to_class($t->name).'Title'));
			}
			asort($tools);
			foreach ($tools as $tool => $title)
			{
				$class = Tool :: type_to_class($tool);
				echo '<option value="'.$tool.'"'. ($class == $this->tool_class ? ' selected="selected"' : '').'>'.htmlentities($title).'</option>';
			}
			echo '</select></form></div>';
			api_display_tool_title(htmlentities(get_lang($this->tool_class.'Title')));
		}
		else
		{
			global $_course;
			echo '<h3>'.htmlentities($_course['name']).'</h3>';
		}
		//echo 'Last visit: '.date('r',$this->get_last_visit_date());
	}
	/**
	 * Displays the footer of this application
	 */
	function display_footer()
	{
		// TODO: Find out why we need to reconnect here.
		global $dbHost, $dbLogin, $dbPass, $mainDbName;
		mysql_connect($dbHost, $dbLogin, $dbPass);
		mysql_select_db($mainDbName);
		Display :: display_footer();
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
	 * Loads the tools installed on the system.
	 */
	private function load_tools()
	{
		if(!is_null($this->get_course_id()))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->tools = $wdm->get_course_modules($this->get_course_id());
			foreach($this->tools as $index => $tool)
			{
				require_once dirname(__FILE__).'/tool/'.$tool->name.'/'.$tool->name.'tool.class.php';
			}
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
		return WeblcmsDataManager :: get_instance()->get_learning_object_publication_attributes($object_id, $type, $offset, $count, $order_property, $order_direction);
	}

	/*
	 * Inherited
	 */
	function count_publication_attributes($type = null, $condition = null)
	{
		return WeblcmsDataManager :: get_instance()->count_publication_attributes($type, $condition);
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
	 * @param string $tool
	 */
	function tool_has_new_publications($tool)
	{
		$class = Tool :: type_to_class($tool);
		$tool_object = new $class ($this);
		if(is_subclass_of($tool_object,'RepositoryTool'))
		{
			$last_visit_date = $this->get_last_visit_date($tool);
			$wdm = WeblcmsDataManager :: get_instance();
			$condition_tool = new EqualityCondition('tool',$tool);
			$condition_date = new InequalityCondition('published',InequalityCondition::GREATER_THAN,$last_visit_date);
			$condition = new AndCondition($condition_tool,$condition_date);
			$new_items = $wdm->count_learning_object_publications($this->get_course_id(),null,null,null,$condition);
			return $new_items > 0;
		}
		return false;
	}
}
?>