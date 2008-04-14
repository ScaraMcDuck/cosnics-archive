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
class TrackingManagerAdminTrackingBrowserComponent extends TrackingManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EventsList')));
		
		if (!$this->get_user() || !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->display_header($trail);

		$cellrenderer = new AdminTrackingBrowserCellRenderer($this);
		
		$previous_events_block = null;
		
		$events = $this->retrieve_events();
		$blockevents = array();
		
		foreach($events as $event)
		{
			if(($event->get_block() != $previous_events_block))
			{
				if($previous_events_block != null) 
					$this->display_table($blockevents, $cellrenderer);
					
				$blockevents = array();
			}

			$blockevents[] = $event;
			$previous_events_block = $event->get_block();
		
		}
		
		$this->display_table($blockevents, $cellrenderer);
		
		$this->display_footer();
	}
	
	function display_table($events, $cellrenderer)
	{
		if(count($events) == 0) return;
		
		echo('<div style="margin-bottom: 5px;"> ' . Translation :: get('Events_for_block') . ': ' . $events[0]->get_block()  . '</div>');
		$eventtable = new SimpleTable($events, $cellrenderer);
		echo $eventtable->toHTML() . '<br />';
	}

}
?>