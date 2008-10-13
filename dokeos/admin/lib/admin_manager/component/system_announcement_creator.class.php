<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../../system_announcer.class.php';

class AdminSystemAnnouncementCreatorComponent extends AdminManagerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishSystemAnnouncement')));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($trail);
		echo $publisher;
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
	
	private function get_publisher_html()
	{		
		$pub = new SystemAnnouncer($this, 'system_announcement', true);
		$html[] =  $pub->as_html();
		
		return implode($html, "\n");
	}
}
?>