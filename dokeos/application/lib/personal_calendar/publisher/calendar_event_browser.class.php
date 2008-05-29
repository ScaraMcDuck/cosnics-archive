<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/browser.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class CalendarEventPublisherBrowserComponent extends PublisherBrowserComponent
{
	function CalendarEventPublisherBrowserComponent($parent)
	{
		parent :: __construct($parent);
		$this->set_browser_actions($this->get_default_browser_actions());
	}
}
?>