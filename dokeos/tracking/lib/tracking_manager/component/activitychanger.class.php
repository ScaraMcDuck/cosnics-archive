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
		$tracker_ids = $_GET[TrackingManager :: PARAM_TRACKER_ID];
		$type = $_GET[TrackingManager :: PARAM_TYPE];
		$event_ids = $_GET[TrackingManager :: PARAM_EVENT_ID];
		
		if (!$this->get_user() || !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if(($type == 'event' && $event_ids) || ($type == 'tracker' && $event_ids && $tracker_ids) || ($type == 'all'))
		{
			switch($type)
			{
				case 'event' :
					$this->change_event_activity($event_ids); break;
				case 'tracker' :
					$this->change_tracker_activity($event_ids, $tracker_ids); break;
				case 'all' :
					$this->change_tracking_activity(); break;
			}
		}
		else
		{
			$this->display_header();
			$this->display_error_message(Translation :: get("NoObjectSelected"));
			$this->display_footer();
		}
	}
	
	/**
	 * Function to change the activity of events
	 * @param Array of event ids
	 */
	function change_event_activity($event_ids)
	{
		if($event_ids)
		{
			if (!is_array($event_ids))
			{
				$event_ids = array ($event_ids);
			}
			
			$success = true;
			
			foreach ($event_ids as $event_id)
			{
				$event = $this->retrieve_event($event_id);
				if(isset($_GET['extra']))
				{
					$event->set_active($_GET['extra'] == 'enable'?1:0);
				}
				else
					$event->set_active(!$event->get_active());
				
				if(!$event->update()) $success = false;
			}
			
			$this->redirect('url', Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(
				TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS));
		}
	}
	
	/**
	 * Function to change the activity of trackers
	 * @param int event_id the event_id
	 * @param array of tracker ids
	 */
	function change_tracker_activity($event_id, $tracker_ids)
	{
		if($tracker_ids)
		{
			if (!is_array($tracker_ids))
			{
				$tracker_ids = array ($tracker_ids);
			}
			
			$success = true;
			
			foreach ($tracker_ids as $tracker_id)
			{ 
				$relation = $this->retrieve_event_tracker_relation($event_id, $tracker_id);
				
				if(isset($_GET['extra']))
				{
					$relation->set_active($_GET['extra'] == 'enable'?1:0);
				}
				else
					$relation->set_active(!$relation->get_active());
					
				if(!$relation->update()) $success = false;
			}
			
			$this->redirect('url', Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(
				TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_VIEW_EVENT, 
				TrackingManager :: PARAM_EVENT_ID => $event_id));
			
		}
	}
	
	/**
	 * Enables / Disables all events and trackers
	 */
	function change_tracking_activity()
	{
		$adm = AdminDataManager :: get_instance();
		$setting = $adm->retrieve_setting_from_variable_name('enable_tracking', 'tracking');
		$setting->set_value($setting->get_value() == 1?0:1);
		$success = $setting->update();
		
		$this->redirect('url', Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(
				TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS));
	}

}
?>