<?php
/**
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__).'/../trackingmanager.class.php';
require_once dirname(__FILE__).'/../trackingmanagercomponent.class.php';
require_once dirname(__FILE__).'/admintrackingbrowser/admintrackingbrowsercellrenderer.class.php';
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
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewEvent')));
		
		if (!$this->get_user() || !$this->get_user()->is_platform_admin() || !isset($_GET['event_id']))
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->display_header($trail);
		
		$event_id = $_GET['event_id'];
		echo Translation :: get('You_are_viewing_trackers_for_event') . ': ' . $event_id . '<br />'; 
		
		$this->display_footer();
	}
	
	function display_table($trackers, $cellrenderer)
	{
		$trackertable = new SimpleTable($trackers, $cellrenderer);
		echo $trackertable->toHTML() . '<br />';
	}

}
?>