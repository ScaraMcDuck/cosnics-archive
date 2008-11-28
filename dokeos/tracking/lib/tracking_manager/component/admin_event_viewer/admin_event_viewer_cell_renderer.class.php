<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Class used to retrieve the modification links for the admin events viewer tables
 */
class AdminEventViewerCellRenderer
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
	function AdminEventViewerCellRenderer($eventviewer, $event)
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
			'label' => ($tracker->get_active() == 1)?Translation :: get('Deactivate'):Translation :: get('Activate'),
			'confirm' => false,
			'img' => ($tracker->get_active() == 1)?
				Theme :: get_common_image_path().'action_visible.png':
				Theme :: get_common_image_path().'action_invisible.png'
		);
		
		$toolbar_data[] = array(
			'href' => $this->eventviewer->get_empty_tracker_url('tracker', $this->event->get_id(), $tracker->get_id()),
			'label' => Translation :: get('Empty_Tracker'),
			'confirm' => true,
			'img' => Theme :: get_common_image_path().'action_recycle_bin.png'
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);

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
					TrackerRegistration :: PROPERTY_CLASS,
					TrackerRegistration :: PROPERTY_PATH
			);
	}
}
?>