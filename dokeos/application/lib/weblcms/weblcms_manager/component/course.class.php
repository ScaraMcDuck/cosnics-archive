<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
/**
 * Weblcms component which provides the course page
 */
class WeblcmsCourseComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$course = $this->get_parameter(Weblcms :: PARAM_COURSE);
		$tool = $this->get_parameter(Weblcms :: PARAM_TOOL);
		$action = $this->get_parameter(Weblcms::PARAM_ACTION);
		$lcms_action = $this->get_parameter(Weblcms::PARAM_LCMS_ACTION);
		$category = $this->get_parameter(Weblcms::PARAM_CATEGORY);
		
		if(is_null($category))
		{
			$category = 0;
		}
		
		if ($course)
		{		
			if($lcms_action)
			{
				$wdm = WeblcmsDataManager :: get_instance();
				switch($lcms_action)
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
			if ($tool && !$lcms_action)
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
		else
		{
			Display :: display_header(get_lang('MyCourses'), 'Mycourses');
			$this->display_footer();
		}
	}
}
?>