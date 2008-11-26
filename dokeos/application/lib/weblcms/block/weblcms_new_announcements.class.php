<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../weblcms_block.class.php';
require_once dirname(__FILE__).'/../course/course_user_category.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/announcement/announcement.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class WeblcmsNewAnnouncements extends WeblcmsBlock
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

		$html[] = $this->display_header();
		
		$dm = WeblcmsDataManager :: get_instance();
		$weblcms = $this->get_parent();
		
		$courses = $weblcms->retrieve_courses($this->get_user_id(), null, null, null, array(Course :: PROPERTY_NAME), array(SORT_ASC));
		
		$items = array();
		
		while($course = $courses->next_result())
		{
			$last_visit_date = $dm->get_last_visit_date($course->get_id(),$this->get_user_id(),'announcement',0);

			$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'announcement');
			$type_condition = new EqualityCondition('type','announcement');
			$publications = $dm->retrieve_learning_object_publications($course->get_id(), null, null, null, $condition, false, array (Announcement :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC), 0, -1, null, $type_condition);
			
			while($publication = $publications->next_result())
			{
				if( $publication->get_publication_date() >= $last_visit_date)
				{
					$items[] = array(
						'course' => $course->get_id(),
						'title' => $publication->get_learning_object()->get_title(),
						'id' => $publication->get_id()
					);
				}
			}
		}
		$html[] = $this->display_new_items($items);
		$html[] = $this->display_footer();

		return implode("\n", $html);
	}
	
	function display_new_items($items)
	{
		$weblcms = $this->get_parent();
		
		$html = array();
		
		if(count($items) > 0)
		{
			$html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
			foreach($items as $item)
			{
				
				$html[] = '<li><a href="'. $weblcms->get_link(array('go' => 'courseviewer', 'application' => 'weblcms', 'tool' => 'announcement', 'tool_action' => 'view', 'pid' => $item['id'], 'course' => $item['course'])) .'">'.$item['title'] .'</a>';
				$html[] = '</li>';
			}
			$html[] = '</ul>';
		}
		else
		{
			$html[] = Translation :: get('NoNewAnnouncements');
		}
		return implode($html, "\n");
	}
}
?>