<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_calendar.class.php';
require_once dirname(__FILE__).'/../personal_calendar_component.class.php';
require_once dirname(__FILE__).'/../../calendar_event_repo_viewer.class.php';
require_once dirname(__FILE__).'/../../publisher/calendar_event_publisher.class.php';

class PersonalCalendarPublisherComponent extends PersonalCalendarComponent
{
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishCalendarEvent')));
		
		$object = $_GET['object'];
		$pub = new CalendarEventRepoViewer($this, 'calendar_event', true);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url(array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'LearningObject: ';
			$publisher = new CalendarEventPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}
		
		$this->display_header($trail);
		//echo $publisher;
		echo implode("\n", $html);
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
}
?>