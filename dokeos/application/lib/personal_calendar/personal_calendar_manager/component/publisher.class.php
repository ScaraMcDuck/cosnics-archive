<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_calendar_manager.class.php';
require_once dirname(__FILE__).'/../personal_calendar_manager_component.class.php';
require_once Path :: get_application_library_path(). 'repo_viewer/repo_viewer.class.php';
require_once dirname(__FILE__).'/../../publisher/calendar_event_publisher.class.php';

class PersonalCalendarManagerPublisherComponent extends PersonalCalendarManagerComponent
{
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendar')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
		
		$object = $_GET['object'];
		$pub = new RepoViewer($this, 'calendar_event', true);
		
		if(!isset($object))
		{	
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