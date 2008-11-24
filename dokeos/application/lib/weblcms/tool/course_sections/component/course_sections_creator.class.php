<?php

require_once dirname(__FILE__).'/../course_sections_tool.class.php';
require_once dirname(__FILE__).'/../course_sections_tool_component.class.php';
require_once dirname(__FILE__).'/../course_section_form.class.php';

class CourseSectionsToolCreatorComponent extends CourseSectionsToolComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
	
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_warning_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		$course_section = new CourseSection();
		$course_section->set_course_code($this->get_course_id());
		$course_section->set_type(CourseSection :: TYPE_TOOL);
		
		$form = new CourseSectionForm(CourseSectionForm :: TYPE_CREATE, $course_section, $this->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CREATE_COURSE_SECTION)));
		
		if($form->validate())
		{
			$success = $form->create_course_section();
			if($success)
			{
				$course_section = $form->get_course_section();
				$this->redirect('url', Translation :: get('CourseSectionCreated'), (false), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
			}
			else
			{
				$this->redirect('url', Translation :: get('CourseSectionNotCreated'), (true), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
			}
		}
		else
		{
			$this->display_header($trail); 
			$form->display();
			$this->display_footer();
		}
	}
}
?>