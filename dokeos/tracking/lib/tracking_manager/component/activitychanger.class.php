<?php
/**
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__).'/../trackingmanager.class.php';
require_once dirname(__FILE__).'/../trackingmanagercomponent.class.php';

/**
 * Component for change of activity
 */
class TrackingManagerActivityChangerComponent extends TrackingManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$tracker_id = $_GET[TrackingManager :: PARAM_TRACKER_ID];
		$type = $_GET[TrackingManager :: PARAM_TYPE];
		$event_id = $_GET[TrackingManager :: PARAM_EVENT_ID];
		
		if(($type == 'event' && $event_id) || ($type == 'tracker' && $event_id && $tracker_id))
		{
			if (!$this->get_user() || !$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail);
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			switch($type)
			{
				case 'event' :
					$object = $this->retrieve_event($event_id); break;
				case 'tracker':
					$object = $this->retrieve_event_tracker_relation($event_id, $tracker_id); break;
			}
			
			$object->set_active(!$object->get_active());
			$success = $object->update();

			switch($type)
			{
				case 'event' : $this->redirect('url', Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS)); break;
				case 'tracker' : $this->redirect('url', Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_VIEW_EVENT, TrackingManager :: PARAM_EVENT_ID => $event_id)); break;
			}
			
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