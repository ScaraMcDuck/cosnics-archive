<?php
/**
 * @package application.lib.profiler.repo_viewer
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/browser.class.php';
/**
 * This class represents a profile repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class CalendarEventRepoViewerBrowserComponent extends RepoViewerBrowserComponent
{
	function CalendarEventRepoViewerBrowserComponent($parent)
	{
		parent :: __construct($parent);
		$this->set_browser_actions($this->get_default_browser_actions());
	}
}
?>