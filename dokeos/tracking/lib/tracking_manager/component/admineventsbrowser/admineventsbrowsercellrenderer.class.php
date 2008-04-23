<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Class used to retrieve the modification links for the admin events browser tables
 */
class AdminEventsBrowserCellRenderer
{
	/**
	 * Browser where this cellrenderer belongs to
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param AdminTrackingBrowser $browser The browser where this renderer belongs to
	 */
	function AdminEventsBrowserCellRenderer($browser)
	{
		$this->browser = $browser;
	}
	
	/**
	 * Creates the modification links for the given event
	 * @param Event $event the event 
	 * @return string The modification links for the given event
	 */
	function get_modification_links($event)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_change_active_url('event', $event->get_id()),
			'label' => ($event->get_active() == 1)?Translation :: get('Deactivate_event'):Translation :: get('Activate_event'),
			'confirm' => false,
			'img' => ($event->get_active() == 1)?
				Path :: get(WEB_LAYOUT_PATH).'img/visible.gif':
				Path :: get(WEB_LAYOUT_PATH).'img/invisible.gif'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_empty_tracker_url('event', $event->get_id()),
			'label' => Translation :: get('Empty_event'),
			'confirm' => true,
			'img' => Path :: get(WEB_LAYOUT_PATH).'img/recycle_bin.gif'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);

	}
	
	/**
	 * Renders a cell
	 * @param string $property the property name
	 * @param Event $event the event
	 */
	function render_cell($property, $event)
	{
		switch($property)
		{
			case Event :: PROPERTY_NAME: if($event->get_active() == 1) return '<a href="' . 
				$this->browser->get_event_viewer_url($event) . '">' . 
				$event->get_default_property($property) . '</a>'; break;
		}
		
		return $event->get_default_property($property);
	}
	
	/**
	 * Returns the properties that will become the columns
	 * @return array of properties
	 */
	function get_properties()
	{
		return array(
					Event :: PROPERTY_NAME,
			);
	}
}
?>