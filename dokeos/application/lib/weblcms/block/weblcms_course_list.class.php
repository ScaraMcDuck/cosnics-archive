<?php
/**
 * @package application.lib.calendar.repo_viewer
 */
require_once dirname(__FILE__).'/../weblcms_block.class.php';
require_once dirname(__FILE__).'/../course/course_user_category.class.php';
/**
 * This class represents a calendar repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class WeblcmsCourseList extends WeblcmsBlock
{
	function run()
	{
		return $this->as_html();
	}
	
	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		
		$weblcms = $this->get_parent();
		$course_categories = $weblcms->retrieve_course_user_categories(null, null, null, array(CourseUserCategory :: PROPERTY_SORT), array(SORT_ASC));
		$courses = $weblcms->retrieve_courses($this->get_user_id(), null, null, null, array(Course :: PROPERTY_NAME), array(SORT_ASC));

		$html[] = $this->display_header();
		$html[] = $this->display_course_digest($courses);
		$html[] = $this->display_footer();

		return implode("\n", $html);
	}
	
	function display_course_digest($courses)
	{
		$html = array();
		if($courses->size() > 0)
		{
			$html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
			while ($course = $courses->next_result())
			{
				$weblcms = $this->get_parent();
				$html[] = '<li><a href="'. $weblcms->get_course_viewing_link($course, true) .'">'.$course->get_name().'</a>';
				//$html[] = '<br />'. $course->get_id() .' - '. $course->get_titular();
				$html[] = '</li>';
			}
			$html[] = '</ul>';
		}
		return implode($html, "\n");
	}
}
?>