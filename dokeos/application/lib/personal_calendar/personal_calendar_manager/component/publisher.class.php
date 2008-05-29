<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_calendar.class.php';
require_once dirname(__FILE__).'/../personal_calendar_component.class.php';
require_once dirname(__FILE__).'/../../calendar_event_publisher.class.php';

class PersonalCalendarPublisherComponent extends PersonalCalendarComponent
{
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishCalendarEvent')));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($trail);
		echo $publisher;
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
	
	private function get_publisher_html()
	{
		$pub = new CalendarEventPublisher($this, 'calendar_event', true);
		$html[] =  $pub->as_html();
		
		return implode($html, "\n");
	}
}
?>