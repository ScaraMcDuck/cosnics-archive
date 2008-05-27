<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Class used to handle the actions from a table
 */
class AdminEventViewerActionHandler
{
	/**
	 * Eventviewer where this Action Handler belongs to
	 */
	private $eventviewer;
	private $event;
	
	/**
	 * Constructor
	 * @param EventViewer $eventviewer the eventviewer where this action handler belongs to
	 * @param Event $event the active event
	 */
	function AdminEventViewerActionHandler($eventviewer, $event)
	{ 
		$this->eventviewer = $eventviewer;
		$this->event = $event;
	}
	
	/**
	 * Method to retrieve the available actions
	 * @return Array of actions name => action
	 */
	 function get_actions()
	 {
	 	return array('enable' => Translation :: get('Enable_selected_trackers'),
	 				 'disable' => Translation :: get('Disable_selected_trackers'),
	 				 TrackingManager :: ACTION_EMPTY_TRACKER => Translation :: get('Empty_selected_trackers'));
	 }
	 
	 /**
	  * Handle's an action that has been triggered
	  * @param array $parameters the parameters for the action (exportvalues of form)
	  */
	 function handle_action($parameters)
	 {
	 	$action = $parameters['action'];

	 	$ids = array();
	 	
	 	foreach($parameters as $key => $parameter)
	 	{ 
	 		if(substr($key, 0, 2) == 'id')
	 		{
	 			$ids[] = substr($key, 2);
	 		}
	 		
	 		if($action == 'enable' || $action == 'disable')
	 		{
	 			$this->eventviewer->redirect('url', null, null, array(
		 				TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_CHANGE_ACTIVE, 
		 				TrackingManager :: PARAM_EVENT_ID => $this->event->get_id(), 
		 				TrackingManager :: PARAM_TRACKER_ID => $ids,
		 				TrackingManager :: PARAM_TYPE => 'tracker',
		 				TrackingManager :: PARAM_EXTRA => $action));
	 		}
	 		else
	 		{
	 			$this->eventviewer->redirect('url', null, null, array(
	 				TrackingManager :: PARAM_ACTION => $action, 
	 				TrackingManager :: PARAM_EVENT_ID => $this->event->get_id(), 
	 				TrackingManager :: PARAM_TRACKER_ID => $ids,
	 				TrackingManager :: PARAM_TYPE => 'tracker'));
	 		}
	 	}
	 }

}
?>