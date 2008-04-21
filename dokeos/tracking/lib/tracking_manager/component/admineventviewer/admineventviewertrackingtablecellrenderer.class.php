<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Class used to retrieve the modification links for the admin events viewer tables
 */
class AdminEventViewerTrackingTableCellRenderer
{
	/**
	 * Eventviewer where this cellrenderer belongs to
	 */
	private $eventviewer;
	private $event;
	
	/**
	 * Constructor
	 * @param AdminTrackingBrowser $browser The browser where this renderer belongs to
	 */
	function AdminEventViewerTrackingTableCellRenderer($eventviewer, $event)
	{
		$this->eventviewer = $eventviewer;
		$this->event = $event;
	}
	
	/**
	 * Creates the modification links for the given tracker
	 * @param Tracker $tracker the tracker 
	 * @return string The modification links for the given tracker
	 */
	function get_modification_links($tracker)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->eventviewer->get_change_active_url('tracker', $this->event->get_id(), $tracker->get_id()),
			'label' => ($tracker->get_active() == 1)?Translation :: get('Hide'):Translation :: get('Visible'),
			'confirm' => false,
			'img' => ($tracker->get_active() == 1)?
				Path :: get(WEB_LAYOUT_PATH).'img/visible.gif':
				Path :: get(WEB_LAYOUT_PATH).'img/invisible.gif'
		);
		
		$toolbar_data[] = array(
			'href' => $this->eventviewer->get_empty_tracker_url($this->event->get_id(), $tracker->get_id()),
			'label' => Translation :: get('Empty_Tracker'),
			'confirm' => true,
			'img' => Path :: get(WEB_LAYOUT_PATH).'img/delete.gif'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);

	}
	
	/**
	 * Renders a cell
	 * @param string $property the property name
	 * @param Tracker $tracker the tracker
	 */
	function render_cell($property, $tracker)
	{
		switch($property)
		{
			/*case Event :: PROPERTY_NAME: return '<a href="' . 
				$this->browser->get_event_viewer_url($event) . '">' . 
				$event->get_default_property($property) . '</a>';*/
		}
		
		return $tracker->get_default_property($property);
	}
	
	/**
	 * Returns the properties that will become the columns
	 * @return array of properties
	 */
	function get_properties()
	{
		return array(
					TrackerRegistration :: PROPERTY_ID,
					TrackerRegistration :: PROPERTY_CLASS,
					TrackerRegistration :: PROPERTY_PATH
			);
	}
}
?>