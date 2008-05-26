<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../weblcmsblock.class.php';
require_once dirname(__FILE__).'/../course/courseusercategory.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class WeblcmsExtra extends WeblcmsBlock
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

		$html[] = '<div class="block" style="background-image: url('.Theme :: get_common_img_path().'block_'.strtolower(PersonalCalendar :: APPLICATION_NAME).'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title() .'<a href="#" class="closeEl">[-]</a></div>';
		$html[] = '<div class="description">';

		$courses = $weblcms->retrieve_courses($this->get_user_id(), null, null, null, null, array(Course :: PROPERTY_NAME), array(SORT_ASC));
		$html[] = $this->display_course_digest($courses);
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
	
	function display_course_digest($courses)
	{
		$html = array();
		if($courses->size() > 0)
		{
			$html[] = '<ul>';
			while ($course = $courses->next_result())
			{
				$weblcms = $this->get_parent();
				$html[] = '<li><a href="'. $weblcms->get_course_viewing_link($course) .'">'.$course->get_name().'</a>';
				//$html[] = '<br />'. $course->get_id() .' - '. $course->get_titular();
				$html[] = '</li>';
			}
			$html[] = '</ul>';
		}
		return implode($html, "\n");
	}
}
?>