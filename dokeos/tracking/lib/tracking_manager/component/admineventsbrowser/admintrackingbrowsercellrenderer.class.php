<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Class used to retrieve the modification links for the admin tracking browser tables
 */
class AdminTrackingBrowserCellRenderer
{
	/**
	 * Browser where this cellrenderer belongs to
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param AdminTrackingBrowser $browser The browser where this renderer belongs to
	 */
	function AdminTrackingBrowserCellRenderer($browser)
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
			'href' => $this->browser->get_change_active_url('event', $event),
			'label' => ($event->get_active() == 1)?Translation :: get('Hide'):Translation :: get('Visible'),
			'confirm' => false,
			'img' => ($event->get_active() == 1)?
				Path :: get(WEB_LAYOUT_PATH).'img/visible.gif':
				Path :: get(WEB_LAYOUT_PATH).'img/invisible.gif'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);

	}
	
	function render_cell($property, $event)
	{
		switch($property)
		{
			case Event :: PROPERTY_NAME: return '<a href="' . 
				$this->browser->get_event_viewer_url($event) . '">' . 
				$event->get_default_property($property) . '</a>';
		}
		
		return $event->get_default_property($property);
	}
	
	function get_properties()
	{
		return array(
					Event :: PROPERTY_ID,
					Event :: PROPERTY_NAME,
			);
	}
}
?>