<?php
/**
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__).'/../trackingmanager.class.php';
require_once dirname(__FILE__).'/../trackingmanagercomponent.class.php';
require_once dirname(__FILE__).'/admineventviewer/admineventviewercellrenderer.class.php';
require_once dirname(__FILE__).'/admineventviewer/admineventvieweractionhandler.class.php';
require_once Path :: get(SYS_LIB_PATH).'/html/table/simpletable.class.php';

/**
 * Component for viewing tracker events 
 */
class TrackingManagerAdminEventViewerComponent extends TrackingManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browser_url(), Translation :: get('EventsList')));
		$trail->add(new Breadcrumb($this->get_url(array(TrackingManager :: PARAM_EVENT_ID => $_GET['event_id'])), Translation :: get('ViewEvent')));
		
		if (!$this->get_user() || !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$event_id = $_GET['event_id'];
		if(!$event_id) return;
		
		$event = $this->retrieve_event($event_id);
		
		$cellrenderer = new AdminEventViewerCellRenderer($this, $event);
		$actionhandler = new AdminEventViewerActionHandler($this, $event);
		
		$trackers = $this->retrieve_trackers_from_event($event_id);
		$trackertable = new SimpleTable($trackers, $cellrenderer, $actionhandler, "trackertable");
		
		$this->display_header($trail);
		
		echo Translation :: get('You_are_viewing_trackers_for_event') . ': ' . $event->get_name() . '<br /><br />'; 
		
		echo $trackertable->toHTML();
		
		$this->display_footer();
	}

}
?>