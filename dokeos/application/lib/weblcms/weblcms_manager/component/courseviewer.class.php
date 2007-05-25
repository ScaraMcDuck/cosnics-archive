<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
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
			Display :: display_error_message(get_lang("NotACourse"));
			$this->display_footer();
			exit;
		}

		if(!$this->is_allowed(VIEW_RIGHT) && !$this->get_user()->is_platform_admin())
		{
			$this->display_header();
			api_not_allowed();
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
				$wdm = WeblcmsDataManager :: get_instance();
				$this->display_header();
				$renderer = ToolListRenderer::factory('FixedLocationToolListRenderer',$this);
				$renderer->display();
				$this->display_footer();
				$wdm->log_course_module_access($this->get_course_id(),$this->get_user_id(),null);
			}
		}
		else
		{
			Display :: display_header(get_lang('MyCourses'), 'Mycourses');
			$this->display_footer();
		}
	}

	function is_allowed($right)
	{
		$user_id = $this->get_user_id();
		$course_id = $this->get_course_id();
		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
		$location_id = RolesRights::get_course_location_id($course_id, TOOL_COURSE_HOMEPAGE);

		$result = RolesRights::is_allowed_which_rights($role_id, $location_id);
		return $result[$right];
	}

	function is_course()
	{
		return ($this->get_course()->get_id() != null ? true : false);
	}
}
?>