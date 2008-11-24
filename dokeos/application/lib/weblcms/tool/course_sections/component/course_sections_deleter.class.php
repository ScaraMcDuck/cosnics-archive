<?php
/**
 */
require_once dirname(__FILE__).'/../course_sections_tool.class.php';
require_once dirname(__FILE__).'/../course_sections_tool_component.class.php';

class CourseSectionsToolDeleterComponent extends CourseSectionsToolComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();

		if (!$user->is_platform_admin())
		{
			$trail = new BreadcrumbTrail();
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}		
		
		$ids = $_GET[CourseSectionsTool :: PARAM_COURSE_SECTION_ID];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$course_section = new CourseSection();
				$course_section->set_id($id);
				
				if (!$course_section->delete())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCourseSectionNotDeleted';
				}
				else
				{
					$message = 'SelectedCourseSectionsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCourseSectionDeleted';
				}
				else
				{
					$message = 'SelectedCourseSectionsDeleted';
				}
			}
			
			$this->redirect('url', Translation :: get($message), ($failures != 0 ? true : false), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCourseSectionsSelected')));
		}
	}
}
?>