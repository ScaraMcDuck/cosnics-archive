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
	const PARAM_COMPLEX_ID = 'cid';
	const PARAM_MOVE = 'move';
	const PARAM_VISIBILITY = 'visible';
	const PARAM_OBJECT_ID = 'object_id';

	const ACTION_PUBLISH = 'publish';
	const ACTION_EDIT = 'edit';
	const ACTION_EDIT_CLOI = 'edit_cloi';
	const ACTION_CREATE_CLOI = 'create_cloi';
	const ACTION_MOVE_UP = 'move_up';
	const ACTION_MOVE_DOWN = 'move_down';
	const ACTION_MOVE_TO_CATEGORY = 'move_to_category';
	const ACTION_MOVE_SELECTED_TO_CATEGORY = 'move_selected_to_category';
	const ACTION_MOVE = 'move';
	const ACTION_DELETE = 'delete';
	const ACTION_DELETE_CLOI = 'delete_cloi';
	const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
	const ACTION_SHOW = 'show';
	const ACTION_HIDE = 'hide';
	const ACTION_PUBLISH_INTRODUCTION = 'publish_introduction';
	const ACTION_PUBLISH_FEEDBACK = 'publish_feedback';
	const ACTION_MANAGE_CATEGORIES = 'managecategories';
	const ACTION_VIEW_ATTACHMENT = 'view_attachment';
	
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
		$this->parse_input_from_table();
	}
	
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']) || isset($_POST['tool_action']))
		{ 
			$ids = $_POST['id'];
			//dump($_POST);
			if (empty ($ids))
			{
				$ids = $_POST['publication_table_id'];
				if(empty($ids))
					$ids = array ();
			}
			elseif (!is_array($ids))
			{
				$ids = array ($ids);
			}

			$action = ($_POST['tool_action'])?$_POST['tool_action']:$_POST['action'];
			
			switch ($action)
			{
				case self :: ACTION_MOVE_SELECTED_TO_CATEGORY :
					$this->set_action(self :: ACTION_MOVE_SELECTED_TO_CATEGORY);
					$_GET[self :: PARAM_PUBLICATION_ID] = $ids;
					break;

				case self :: ACTION_DELETE :
					$this->set_action(self :: ACTION_DELETE);
					$_GET[self :: PARAM_PUBLICATION_ID] = $ids;
					break;

                case self :: ACTION_HIDE :
					$this->set_action(self :: ACTION_HIDE);
					$_GET[self :: PARAM_PUBLICATION_ID] = $ids;
					break;

                case self :: ACTION_SHOW :
					$this->set_action(self :: ACTION_SHOW);
					$_GET[self :: PARAM_PUBLICATION_ID] = $ids;
					break;
			}
		}
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
			case self :: ACTION_PUBLISH_FEEDBACK :
				$component = ToolComponent :: factory('', 'FeedbackPublisher', $this);
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
			case self :: ACTION_MOVE:
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
			case self :: ACTION_DELETE_CLOI :
				$component = ToolComponent :: factory('', 'ComplexDeleter', $this);
				break;
			case self :: ACTION_EDIT_CLOI :
				$component = ToolComponent :: factory('', 'ComplexEdit', $this);
				break;
			case self :: ACTION_CREATE_CLOI :
				$component = ToolComponent :: factory('', 'ComplexCreator', $this);
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
			case self :: ACTION_VIEW_ATTACHMENT:
				$component = ToolComponent :: factory('', 'AttachmentViewer', $this);
				break;
		}
		if($component)
		{
			$component->run();
		}
		
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
	function display_header($breadcrumbtrail)
	{
		$trail = new BreadcrumbTrail();
		switch($this->parent->get_course()->get_breadcrumb())
		{
			case Course :: BREADCRUMB_TITLE : $title = $this->parent->get_course()->get_name(); break;
			case Course :: BREADCRUMB_CODE : $title = $this->parent->get_course()->get_visual(); break;
			case Course :: BREADCRUMB_COURSE_HOME : $title = Translation :: get('CourseHome'); break;
			default: $title = $this->parent->get_course()->get_visual(); break;
		}
		
		$trail->add(new Breadcrumb($this->get_url(null, false, true, array('tool')), $title));
		
		// TODO: do this by overriding display_header in the course_group tool
		
		if(!is_null($this->parent->get_course_group()))
		{
			$course_group = $this->parent->get_course_group();
			$trail->add(new Breadcrumb($this->get_url(array('tool_action' => null, Weblcms::PARAM_COURSE_GROUP=>null)),Translation :: get('CourseGroups')));
			$trail->add(new Breadcrumb($this->get_url(array('tool_action' => null)), $course_group->get_name()));
		}
		elseif($this->get_tool_id() == 'course_group')
		{
			$trail->add(new Breadcrumb($this->get_url(array('tool_action' => null)), Translation :: get(Tool :: type_to_class($this->parent->get_tool_id()) . 'Title')));
		}
		// TODO: make this the default
		if($this->get_tool_id() != 'course_group')
		{
			$trail->add(new Breadcrumb($this->get_url(array('tool_action' => null)), Translation :: get(Tool :: type_to_class($this->parent->get_tool_id()) . 'Title')));
		}
		
		$breadcrumbs = $breadcrumbtrail->get_breadcrumbs();
		
		if (count($breadcrumbs))
		{
			foreach ($breadcrumbs as $i => $breadcrumb)
			{
				if ($i != 0)
					$trail->add($breadcrumb);
			}
		}
		$this->parent->display_header($trail);
		//echo '<div class="clear"></div>';
		
		if($this->parent->get_course()->get_tool_shortcut() == Course :: TOOL_SHORTCUT_ON)
		{
			$renderer = ToolListRenderer::factory('Shortcut', $this->parent);
			echo '<div style="width: 100%; text-align: right;">';
			$renderer->display();
			echo '</div>';
		}
		
		echo '<div class="clear"></div><br />';
		
		if ($msg = $_GET[Weblcms :: PARAM_MESSAGE])
		{
			$this->parent->display_message($msg);
		}
		if($msg = $_GET[Weblcms :: PARAM_ERROR_MESSAGE])
		{
			$this->parent->display_error_message($msg);
		}
		
		
		$menu_style = $this->parent->get_course()->get_menu();
		if($menu_style != Course :: MENU_OFF)
		{
			$renderer = ToolListRenderer::factory('Menu', $this->parent);
			$renderer->display();					
			echo '<div id="tool_browser_'. ($renderer->display_menu_icons() && !$renderer->display_menu_text() ? 'icon_' : '') . $renderer->get_menu_style() .'">';
		}
		else
		{
			echo '<div id="tool_browser">';
		}
	}
	/**
	 * @see Application :: display_footer()
	 */
	function display_footer()
	{
		echo '</div>';
		$this->parent->display_footer();
	}
	
	function display_error_message($message)
	{
		$this->parent->display_error_message($message);
	}

	/**
	 * Informs the user that access to the page was denied.
	 */
	function disallow()
	{
		Display :: not_allowed();
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
		
		$this->rights[VIEW_RIGHT] = false;
		$this->rights[EDIT_RIGHT] = false;
		$this->rights[ADD_RIGHT] = false;
		$this->rights[DELETE_RIGHT] = false;
		$user = $this->get_user();
		$course = $this->get_course();
		if ($user != null && $course != null)
		{
			$relation = $this->parent->retrieve_course_user_relation($course->get_id(),$user->get_id());
			
			if($relation && $relation->get_status() == 5 && $this->properties->visible == 1)
			{
				$this->rights[VIEW_RIGHT] = true;
			}
			if(($relation && $relation->get_status() == 1) || $user->is_platform_admin())
			{
				$this->rights[VIEW_RIGHT] = true;
				$this->rights[EDIT_RIGHT] = true;
				$this->rights[ADD_RIGHT] = true;
				$this->rights[DELETE_RIGHT] = true;
			}
		}
		return;
		//$tool_id = $this->get_tool_id();
		
		// Roles and rights system
		//$user_id = $this->get_user_id();
		//$course_id = $this->get_course_id();
		
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
		//$form->addElement('submit', 'submit', Translation :: get('Ok'));
		$buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive move'));
		$buttons[] = $form->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		$parameters = $this->get_parameters();
		$parameters['pcattree'] = $_GET['pcattree'];
		$parameters[self :: PARAM_ACTION] = $action;
		foreach($parameters as $key => $value)
		{
			$form->addElement('hidden',$key,$value);
		}
		return $form;
	}
	
	function display_introduction_text($introduction_text)
	{
		$html = array();
		
		if($introduction_text)
		{
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);
			
			$html[] = '<div class="announcements level_1">';
			$html[] = '<div class="title">';
			$html[] = $introduction_text->get_learning_object()->get_title();
			$html[] = '</div><div class="clear">&nbsp;</div>';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_learning_object()->get_description();
			$html[] = '</div>';
			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}
		
		return implode("\n",$html);
	}

	static function get_allowed_types()
	{
		return array();
	}
}
?>