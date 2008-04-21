<?php
/**
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__).'/../trackingmanager.class.php';
require_once dirname(__FILE__).'/../trackingmanagercomponent.class.php';

/**
 * Component to empty a tracker
 */
class TrackingManagerEmptyTrackerComponent extends TrackingManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$tracker_id = $_GET[TrackingManager :: PARAM_TRACKER_ID];
		$event_id = $_GET[TrackingManager :: PARAM_EVENT_ID];
		
		if($tracker_id)
		{
			if (!$this->get_user() || !$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail);
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$trackerregistration = $this->retrieve_tracker_registration($tracker_id);
			$event = $this->retrieve_event($event_id);
			
			$classname = $trackerregistration->get_class();
			$filename = RepositoryUtilities :: camelcase_to_underscores($classname);

			$fullpath = Path :: get(SYS_PATH) . $trackerregistration->get_path() . 
				strtolower($filename) . '.class.php';
			require_once($fullpath);
			
			$tracker = new $classname;
			$success = $tracker->empty_tracker($event);

			$this->redirect('url', Translation :: get($success ? 'TrackerEmpty' : 'TrackerNotEmpty'), ($success ? false : true), array(TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_VIEW_EVENT, TrackingManager :: PARAM_EVENT_ID => $event_id)); break;
			
		}
		else
		{
			$this->display_header();
			$this->display_error_message(Translation :: get("NoObjectSelected"));
			$this->display_footer();
		}
	}

}
?>