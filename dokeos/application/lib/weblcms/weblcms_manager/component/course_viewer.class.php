<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_component.class.php';
/**
 * Weblcms component which provides the course page
 */
class WeblcmsCourseViewerComponent extends WeblcmsComponent
{
	/**
	 * The tools that this application offers.
	 */
	private $tools;
	/**
	 * The class of the tool currently active in this application
	 */
	private $tool_class;

	/**
	 * The course object of the course currently active in this application
	 */
	private $course;


	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if(!$this->is_course())
		{
			$this->display_header();
			Display :: display_error_message(Translation :: get("NotACourse"));
			$this->display_footer();
			exit;
		}
		
		$this->load_course_theme();
		$this->load_course_language();

		/**
		 * Here we set the rights depending on the user status in the course.
		 * This completely ignores the roles-rights library.
		 * TODO: WORK NEEDED FOR PROPPER ROLES-RIGHTS LIBRARY
		 */

		$user = $this->get_user();
		$course = $this->get_course();
		$relation = $this->retrieve_course_user_relation($course->get_id(),$user->get_id());
		
		if($relation->get_status() != 5 && $relation->get_status() != 1 && !$user->is_platform_admin())
		//TODO: Roles & Rights
		//if(!$this->is_allowed(VIEW_RIGHT) && !$this->get_user()->is_platform_admin())
		{
			$this->display_header();
			Display :: display_not_allowed();
			$this->display_footer();
			exit;
		}

		$course = $this->get_parameter(Weblcms :: PARAM_COURSE);
		$tool = $this->get_parameter(Weblcms :: PARAM_TOOL);
		$action = $this->get_parameter(Weblcms::PARAM_ACTION);
		$component_action = $this->get_parameter(Weblcms::PARAM_COMPONENT_ACTION);
		$category = $this->get_parameter(Weblcms::PARAM_CATEGORY);

		if(is_null($category))
		{
			$category = 0;
		}

		if ($course)
		{
			if($component_action)
			{
				$wdm = WeblcmsDataManager :: get_instance();
				switch($component_action)
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
				$this->set_parameter(Weblcms :: PARAM_TOOL, null);
			}
			if ($tool && !$component_action)
			{
				$wdm = WeblcmsDataManager :: get_instance();
				$class = Tool :: type_to_class($tool);
				$toolObj = new $class ($this->get_parent());
				$this->set_tool_class($class);
				$toolObj->run();
				$wdm->log_course_module_access($this->get_course_id(),$this->get_user_id(),$tool,$category);
			}
			else
			{
				$trail = new BreadcrumbTrail();
				$trail->add(new Breadcrumb($this->get_url(), $this->get_course_id()));
				
				$wdm = WeblcmsDataManager :: get_instance();
				$this->display_header($trail);
				
				echo $this->display_introduction_text();
				echo '<div class="clear"></div>';
				//TODO: Depending on settings, display menu and/or shortcut icons
				//Display shortcut icons
				//$renderer = ToolListRenderer::factory('Menu',$this);
				//$renderer->set_type(MenuToolListRenderer::MENU_TYPE_TOP_NAVIGATION);
				//$renderer->display();
				//Display menu
				//$renderer = ToolListRenderer::factory('Menu',$this);
				//$renderer->display();
				$renderer = ToolListRenderer::factory('FixedLocation',$this);
				$renderer->display();
				$this->display_footer();
				$wdm->log_course_module_access($this->get_course_id(),$this->get_user_id(),null);
			}
		}
		else
		{
			Display :: display_header(Translation :: get('MyCourses'), 'Mycourses');
			$this->display_footer();
		}
	}

// TODO: New Roles & Rights system
//	function is_allowed($right)
//	{
//		$user_id = $this->get_user_id();
//		$course_id = $this->get_course_id();
//		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
//		$location_id = RolesRights::get_course_location_id($course_id, TOOL_COURSE_HOMEPAGE);
//
//		$result = RolesRights::is_allowed_which_rights($role_id, $location_id);
//		return $result[$right];
//	}

	function is_course()
	{
		return ($this->get_course()->get_id() != null ? true : false);
	}
	
	function load_course_theme()
	{
		$course_can_have_theme = $this->get_platform_setting('allow_course_theme_selection');
		$course = $this->get_course();
		
		if ($course_can_have_theme && $course->has_theme())
		{
			Theme :: set_theme($course->get_theme());
		}
	}
	
	function load_course_language()
	{
		$course = $this->get_course();
		Translation :: set_language($course->get_language());
	}
	
	function display_introduction_text()
	{
		$html = array();
		
	
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','introduction'));
		$introduction_text = $publications->next_result();
		
		if($introduction_text)
		{
			$html[] = '<div class="learning_object">';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_learning_object()->get_description();
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}
		else
		{
			$tb_data[] = array(
				'href' => $this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_PUBLISH_INTRODUCTION)),
				'label' => Translation :: get('PublishIntroductionText'),
				'img' => Theme :: get_common_img_path() . 'action_publish.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
				
			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
		}
		
		return implode("\n",$html);
	}
	
}
?>