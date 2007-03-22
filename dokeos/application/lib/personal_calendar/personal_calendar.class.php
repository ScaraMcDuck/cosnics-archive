<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../webapplication.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/renderer/personal_calendar_list_renderer.class.php';
require_once dirname(__FILE__).'/connector/personal_calendar_weblcms_connector.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class PersonalCalendar extends WebApplication
{
	/**
	 * The owner of this personal calendar
	 */
	private $user_id;
	/**
	 * Constructor
	 * @param int $user_id
	 */
	public function PersonalCalendar($user_id)
	{
		$this->user_id = $user_id;
	}
	/**
	 * Runs the personal calendar application
	 */
	public function run()
	{
		$renderer = new PersonalCalendarListRenderer($this);
		Display :: display_header();
		echo $renderer->render();
		Display :: display_footer();
	}
	/**
	 * Gets the events
	 * @param int $from_date
	 * @param int $to_date
	 */
	public function get_events($from_date,$to_date)
	{
		$connector = new PersonalCalendarWeblcmsConnector();
		return $connector->get_events($this->user_id,$from_date,$to_date);
	}
	public function learning_object_is_published($object_id)
	{
		return false;
	}
	public function any_learning_object_is_published($object_ids)
	{
		return false;
	}
	public function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return null;
	}
	public function get_learning_object_publication_attribute($object_id)
	{
		return null;
	}
	public function count_publication_attributes($type = null, $condition = null)
	{
		return 0;
	}
	public function delete_learning_object_publications($object_id)
	{
		return 0;
	}
	public function update_learning_object_publication_id($publication_attr)
	{
		return 0;
	}
}
?>