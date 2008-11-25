<?php

require_once dirname(__FILE__).'/../course_sections_tool.class.php';
require_once dirname(__FILE__).'/../course_sections_tool_component.class.php';
require_once dirname(__FILE__).'/../course_section_tool_selector_form.class.php';

class CourseSectionsToolToolSelectorComponent extends CourseSectionsToolComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$trail = new BreadcrumbTrail();
		
		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		$id = $_GET[CourseSectionsTool :: PARAM_COURSE_SECTION_ID];
		if ($id)
		{
			$course_section = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $id))->next_result();
			
			$form = new CourseSectionToolSelectorForm($course_section, $this->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_SELECT_TOOLS_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $id)));
	
			if($form->validate())
			{
				$success = $form->update_course_modules();
				$this->redirect('url', Translation :: get($success ? 'CourseSectionUpdated' : 'CourseSectionNotUpdated'), ($success ? false : true), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
			}
			else
			{
				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCourseSectionSelected')));
		}
	}
}
?>