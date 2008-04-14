<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_calendar.class.php';
require_once dirname(__FILE__).'/../personalcalendarcomponent.class.php';
require_once dirname(__FILE__).'/../../calendareventpublisher.class.php';

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
		// TODO: Uniformize the PersonalCalendarPublisher cfr. other Publishers
		$pub = new CalendarEventPublisher($this);
		$html[] =  $pub->as_html();
		
		return implode($html, "\n");
	}
}
?>