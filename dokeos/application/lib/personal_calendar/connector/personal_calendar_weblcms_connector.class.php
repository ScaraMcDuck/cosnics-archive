<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_connector.class.php');
require_once (dirname(__FILE__).'/../../weblcms/weblcmsdatamanager.class.php');
/**
 * This personal calendar connector allows the personal calendar to retrieve the
 * published calendar events in the WebLcms application.
 */
class PersonalCalendarWeblcmsConnector implements PersonalCalendarConnector
{
	/**
	 * @see PersonalCalendarConnector
	 */
	public function get_events($user_id, $from_date, $to_date)
	{
		$dm = WeblcmsDatamanager :: get_instance();
		$conditions = array();
		$conditions[] = new EqualityCondition('tool', 'calendar');
		$conditions[] = new EqualityCondition('hidden',0);
		$condition = new AndCondition($conditions);
		$publications = $dm->retrieve_learning_object_publications(null, null, $user_id, null, $condition);
		$result = array ();
		while ($publication = $publications->next_result())
		{
			$event = $publication->get_learning_object();
			if($event->get_start_date() >= $from_date && $event->get_start_date() <= $to_date)
			{
				$info = new LearningObjectPublicationAttributes();
				$info->set_id($publication->get_id());
				$info->set_publisher_user_id($publication->get_publisher_id());
				$info->set_publication_date($publication->set_publication_date());
				$info->set_application('weblcms');
				//TODO: i8n location string
				$info->set_location($publication->get_course_id().' &gt; '.$publication->get_tool());
				//TODO: set correct URL
				$info->set_url('index_weblcms.php?go=courseviewer&amp;course='.$publication->get_course_id().'&amp;tool='.$publication->get_tool().'&amp;pid='.$publication->get_id());
				$info->set_publication_object_id($publication->get_learning_object()->get_id());
				$result[] = $info;
			}
		}
		return $result;
	}
}
?>