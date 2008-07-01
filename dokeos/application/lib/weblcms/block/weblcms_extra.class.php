<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../weblcms_block.class.php';
require_once dirname(__FILE__).'/../course/course_user_category.class.php';
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

		$html[] = '<div class="block" id="block_'. $this->get_block_info()->get_id() .'" style="background-image: url('.Theme :: get_img_path().'block_'.strtolower(Weblcms :: APPLICATION_NAME).'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title() .'<a href="#" class="closeEl"><img class="visible"'. ($this->get_block_info()->is_visible() ? ' style="display: block"' : ' style="display: none"') .' src="'.Theme :: get_common_img_path().'action_visible.png" /><img class="invisible"'. ($this->get_block_info()->is_visible() ? ' style="display: none"' : ' style="display: block"') .' src="'.Theme :: get_common_img_path().'action_invisible.png" /></a></div>';
		$html[] = '<div class="description"'. ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') .'>';

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