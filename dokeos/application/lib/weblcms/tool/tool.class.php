<?php
/**
 * $Id$
 * Tool
 * @package application.weblcms.tool
 */

require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
==============================================================================
 *	This is the base class for all tools used in applications.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class Tool
{
	const PARAM_ACTION = 'tool_action';
	const PARAM_PUBLICATION_ID = 'pid';
	const PARAM_MOVE = 'move';
	const PARAM_VISIBILITY = 'visible';

	const ACTION_PUBLISH = 'publish';
	const ACTION_EDIT = 'edit';
	const ACTION_MOVE_UP = 'move_up';
	const ACTION_MOVE_DOWN = 'move_down';
	const ACTION_MOVE_TO_CATEGORY = 'move_to_category';
	const ACTION_MOVE_SELECTED_TO_CATEGORY = 'move_selected_to_category';
	const ACTION_DELETE = 'delete';
	const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
	const ACTION_SHOW = 'show';
	const ACTION_HIDE = 'hide';
	const ACTION_PUBLISH_INTRODUCTION = 'publish_introduction';
	const ACTION_MANAGE_CATEGORIES = 'managecategories';
	
	/**
	 * The action of the tool
	 */
	private $action;
	
	/**
	 * The application that the tool is associated with.
	 */
	private $parent;

	/**
	 * The rights of the current user in this tool
	 */
	private $rights;

	/**
	 * Constructor.
	 * @param Application $parent The application that the tool is associated
	 *                            with.
	 */
	function Tool($parent)
	{
		$this->parent = $parent;
		$this->properties = $parent->get_tool_properties($this->get_tool_id());
		$this->load_rights();
		$this->set_action(isset($_POST[self :: PARAM_ACTION]) ? $_POST[self :: PARAM_ACTION] : $_GET[self :: PARAM_ACTION]);
	}
	
	function set_action($action)
	{
		$this->action = $action;
	}
	
	function get_action()
	{
		return $this->action;
	}

	/**
	 * Runs the tool, performing whatever actions are necessary.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;

		switch ($action)
		{
			case self :: ACTION_EDIT :
				$component = ToolComponent :: factory('', 'Edit', $this);
				break;
			case self :: ACTION_PUBLISH_INTRODUCTION : 
				$component = ToolComponent :: factory('', 'IntroductionPublisher', $this);
				break;
			case self :: ACTION_MANAGE_CATEGORIES :
				$component = ToolComponent :: factory('', 'CategoryManager', $this);
				break;
			case self :: ACTION_MOVE_UP:
				$_GET[self :: PARAM_MOVE] = 1;
				$component = ToolComponent :: factory('', 'Move', $this);
				break;
			case self :: ACTION_MOVE_DOWN:
				$_GET[self :: PARAM_MOVE] = -1;
				$component = ToolComponent :: factory('', 'Move', $this);
				break;
			case self :: ACTION_MOVE_TO_CATEGORY:
				$component = ToolComponent :: factory('', 'MoveSelectedToCategory', $this);
				break;
			case self :: ACTION_MOVE_SELECTED_TO_CATEGORY:
				$component = ToolComponent :: factory('', 'MoveSelectedToCategory', $this);
				break;
			case self :: ACTION_DELETE :
				$component = ToolComponent :: factory('', 'Delete', $this);
				break;
			case self :: ACTION_TOGGLE_VISIBILITY :
				$component = ToolComponent :: factory('', 'ToggleVisibility', $this);
				break;
			case self :: ACTION_SHOW :
				$_GET[PARAM_VISIBILITY] = 0;
				$component = ToolComponent :: factory('', 'ToggleVisibility', $this);
				break;
			case self :: ACTION_HIDE :
				$_GET[PARAM_VISIBILITY] = 1;
				$component = ToolComponent :: factory('', 'ToggleVisibility', $this);
				break;
		}
		if($component)
			$component->run();
		
		return $component;
	}

	/**
	 * Returns the application that this tool is associated with.
	 * @return Application The application.
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	/**
	 * Returns the properties of this tool within the specified course.
	 * @return Tool The tool.
	 */
	function get_properties()
	{
		return $this->properties;
	}	

	/**
	 * @see Application :: get_tool_id()
	 */
	function get_tool_id()
	{
		return $this->parent->get_tool_id();
	}
	/**
	 * @see Application :: display_header()
	 */
	function display_header($breadcrumbtrail, $append = array())
	{
		$breadcrumbtrail->add(new Breadcrumb($this->get_url(null, false, true, array('tool')), $_GET[Weblcms :: PARAM_COURSE]));
		
		if(!is_null($this->parent->get_course_group()))
		{
			$course_group = $this->parent->get_course_group();
			$breadcrumbtrail->add(new Breadcrumb($this->get_url(array('tool_action' => null, Weblcms::PARAM_COURSE_GROUP=>null)),Translation :: get('CourseGroups')));
			$breadcrumbtrail->add(new Breadcrumb($this->get_url(array('tool_action' => null)), $course_group->get_name()));
		}
		// TODO: do this by overriding display_header in the course_group tool
		elseif($this->get_tool_id() == 'course_group')
		{
			$breadcrumbtrail->add(new Breadcrumb($this->get_url(array('tool_action' => null)), Translation :: get(Tool :: type_to_class($this->get_tool_id()).'Title')));
		}
		// TODO: make this the default
		if($this->get_tool_id() != 'course_group')
		{
			$breadcrumbtrail->add(new Breadcrumb($this->get_url(array('tool_action' => null)), Translation :: get(Tool :: type_to_class($this->get_tool_id()).'Title')));
		}
		if (count($append))
		{
			foreach ($append as $extra)
			{
				$breadcrumbtrail->add($extra);
			}
		}
		$this->parent->display_header($breadcrumbtrail);
		echo '<div class="clear"></div>';
		$renderer = ToolListRenderer::factory('Menu',$this->parent);
		$renderer->set_type(MenuToolListRenderer::MENU_TYPE_TOP_NAVIGATION);
		echo '<div style="width: 100%; text-align: right;">';
		$renderer->display();
		echo '</div>';
	}
	/**
	 * @see Application :: display_footer()
	 */
	function display_footer()
	{
		$this->parent->display_footer();
	}

	/**
	 * Informs the user that access to the page was denied.
	 */
	function disallow()
	{
		Display :: display_not_allowed();
	}

	/**
	 * @see WebApplication :: get_user()
	 */
	function get_user()
	{
		return $this->parent->get_user();
	}

	/**
	 * @see WebApplication :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->parent->get_user_id();
	}

	function get_user_info($user_id)
	{
		return $this->parent->get_user_info($user_id);
	}

	/**
	 * @see WebApplication :: get_course_id()
	 */
	function get_course()
	{
		return $this->parent->get_course();
	}

	/**
	 * @see WebApplication :: get_course_id()
	 */
	function get_course_id()
	{
		return $this->parent->get_course_id();
	}

	/**
	 * @see WebApplication :: get_course_groups()
	 */
	function get_course_groups()
	{
		return $this->parent->get_course_groups();
	}
	
	function get_course_group()
	{
		return $this->parent->get_course_group();
	}

	/**
	 * @see WebApplication :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	/**
	 * @see WebApplication :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->parent->get_parameter($name);
	}

	/**
	 * @see WebApplication :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

	/**
	 * @see WebApplication :: get_url()
	 */
	function get_url($parameters = array(), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->parent->get_url($parameters, $encode, $filter, $filterOn);
	}

	/**
	 * @see WebApplication :: get_url()
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = null)
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}

	/**
	 * Check if the current user has a given right in this tool
	 * @param int $right
	 * @return boolean True if the current user has the right
	 */
	function is_allowed($right)
	{
		return $this->rights[$right];
	}

	/**
	 * Load the rights for the current user in this tool
	 */
	private function load_rights()
	{
		/**
		 * Here we set the rights depending on the user status in the course.
		 * This completely ignores the roles-rights library.
		 * TODO: WORK NEEDED FOR PROPPER ROLES-RIGHTS LIBRARY
		 */
		$user = $this->get_user();
		$course = $this->get_course();
		$relation = $this->parent->retrieve_course_user_relation($course->get_id(),$user->get_id());
		$this->rights[VIEW_RIGHT] = false;
		$this->rights[EDIT_RIGHT] = false;
		$this->rights[ADD_RIGHT] = false;
		$this->rights[DELETE_RIGHT] = false;
		if($relation->get_status() == 5 && $this->properties->visible == 1)
		{
			$this->rights[VIEW_RIGHT] = true;
		}
		if($relation->get_status() == 1 || $user->is_platform_admin())
		{
			$this->rights[VIEW_RIGHT] = true;
			$this->rights[EDIT_RIGHT] = true;
			$this->rights[ADD_RIGHT] = true;
			$this->rights[DELETE_RIGHT] = true;
		}
		return;
		$tool_id = $this->get_tool_id();
		
		// Roles and rights system
		$user_id = $this->get_user_id();
		$course_id = $this->get_course_id();
		
		// TODO: New Roles & Rights system
//		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
//		$location_id = RolesRights::get_course_tool_location_id($course_id, $tool_id);
//		$this->rights = RolesRights::is_allowed_which_rights($role_id, $location_id);
	}

	/**
	 * Converts a tool name to the corresponding class name.
	 * @param string $tool The tool name.
	 * @return string The class name.
	 */
	static function type_to_class($tool)
	{
		return DokeosUtilities :: underscores_to_camelcase($tool).'Tool';
	}

	/**
	 * Converts a tool class name to the corresponding tool name.
	 * @param string $class The class name.
	 * @return string The tool name.
	 */
	static function class_to_type($class)
	{
		return str_replace('/Tool$/', '', DokeosUtilities :: camelcase_to_underscores($class));
	}
	/**
	 * @see WebLcms::get_last_visit_date()
	 */
	function get_last_visit_date()
	{
		return $this->parent->get_last_visit_date();
	}
	
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	/** Dummy functions so we can use the same component class for both tool and repositorytool **/
	function perform_requested_action()
	{
	}
	
//	function get_categories($list = false)
//	{
//		return $this->get_parent()->get_categories($list);
//	}

	/**
	 * @see Application :: get_category()
	 */
	function get_category($id)
	{
		return $this->get_parent()->get_category($id);
	}
	
	private function build_move_to_category_form($action)
	{
		$form = new FormValidator($action,'get',$this->get_url());
		$categories = $this->get_categories(true);
		$form->addElement('select', LearningObjectPublication :: PROPERTY_CATEGORY_ID, Translation :: get('Category'), $categories);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
		$parameters = $this->get_parameters();
		$parameters['pcattree'] = $_GET['pcattree'];
		$parameters[self :: PARAM_ACTION] = $action;
		foreach($parameters as $key => $value)
		{
			$form->addElement('hidden',$key,$value);
		}
		return $form;
	}

}
?>