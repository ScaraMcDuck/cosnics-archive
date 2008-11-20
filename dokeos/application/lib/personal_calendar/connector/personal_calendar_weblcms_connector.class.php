<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_connector.class.php');
require_once (dirname(__FILE__).'/../../weblcms/weblcms_data_manager.class.php');
/**
 * This personal calendar connector allows the personal calendar to retrieve the
 * published calendar events in the WebLcms application.
 */
class PersonalCalendarWeblcmsConnector implements PersonalCalendarConnector
{
	/**
	 * @see PersonalCalendarConnector
	 */
	public function get_events($user, $from_date, $to_date)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$course_groups = $dm->retrieve_course_groups_from_user($user)->as_array();
		$conditions = array();
		$conditions[] = new EqualityCondition('tool', 'calendar');
		$conditions[] = new EqualityCondition('hidden',0);
		$condition = new AndCondition($conditions);
		$publications = $dm->retrieve_learning_object_publications(null, null, $user->get_id(), $course_groups, $condition, false, array (), array (), 0, -1, null, new EqualityCondition('type', 'calendar_event'));
		$result = array ();
		while ($publication = $publications->next_result())
		{
			$object = $publication->get_learning_object();
			
			if ($object->repeats())
			{
				$repeats = $object->get_repeats($from_date, $to_date);
				
				foreach($repeats as $repeat)
				{
					$event = new PersonalCalendarEvent();
					$event->set_start_date($repeat->get_start_date());
					$event->set_end_date($repeat->get_end_date());
					$event->set_url('run.php?application=weblcms&amp;go=courseviewer&amp;course='.$publication->get_course_id().'&amp;tool='.$publication->get_tool().'&amp;pid='.$publication->get_id());
					$event->set_title($repeat->get_title());
					$event->set_content($repeat->get_description());
					$event->set_source('weblcms');
					
					$result[] = $event;
				}
			}
			elseif($object->get_start_date() >= $from_date && $object->get_start_date() <= $to_date)
			{
				$event = new PersonalCalendarEvent();
				$event->set_start_date($object->get_start_date());
				$event->set_end_date($object->get_end_date());
				$event->set_url('run.php?application=weblcms&amp;go=courseviewer&amp;course='.$publication->get_course_id().'&amp;tool='.$publication->get_tool().'&amp;pid='.$publication->get_id());
				$event->set_title($object->get_title());
				$event->set_content($object->get_description());
				$event->set_source('weblcms');
				
				$result[] = $event;
			}
		}
		return $result;
	}
}
?>