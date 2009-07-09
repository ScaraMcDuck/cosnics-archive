<?php
/**
 * @package application.lib.calendar.repo_viewer
 */
require_once dirname(__FILE__).'/../weblcms_block.class.php';
require_once dirname(__FILE__).'/../course/course_user_category.class.php';
require_once dirname(__FILE__).'/../tool/tool.class.php';
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
		$course_categories = $weblcms->retrieve_course_user_categories(null, null, null, new ObjectTableOrder(CourseUserCategory :: PROPERTY_SORT));
		$courses = $weblcms->retrieve_courses($this->get_user_id(), null, null, null, new ObjectTableOrder(Course :: PROPERTY_NAME));

		$html[] = $this->display_header();
		$html[] = $this->display_course_digest($courses);
		$html[] = $this->display_footer();

		return implode("\n", $html);
	}
	
	function display_course_digest($courses)
	{
		$html = array();
		$wdm = WeblcmsDataManager :: get_instance();
		
		if($courses->size() > 0)
		{
			$html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
			while ($course = $courses->next_result())
			{
				$tools = $wdm->get_course_modules($course->get_id());
				$weblcms = $this->get_parent();
				$weblcms->set_course($course);
				$html[] = '<li><a href="'. $weblcms->get_course_viewing_link($course, true) .'">'.$course->get_name().'</a>';
				//$html[] = '<br />'. $course->get_id() .' - '. $course->get_titular();
		
				foreach($tools as $index => $tool)
				{					  
					require_once dirname(__FILE__).'/../tool/'.$tool->name.'/'.$tool->name.'_tool.class.php';
					
					if($tool->visible && $weblcms->tool_has_new_publications($tool->name))
					{
						$params[WeblcmsManager :: PARAM_TOOL] = $tool->name;
						$params[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
						$params[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE;
						$url = 'run.php?application=weblcms';
						
						foreach($params as $key => $param)
						{
							$url .= '&' . $key . '=' . $param;
						}
						
						$html[] = '<a href="'.$url.'"><img src="'. Theme :: get_image_path('weblcms'). 'tool_' . $tool->name.'_new.png" alt="'.Translation :: get('New').'"/></a>';
					}
				}
				
				$html[] = '</li>';
				
			}
			$html[] = '</ul>';
		}
		return implode($html, "\n");
	}
}
?>