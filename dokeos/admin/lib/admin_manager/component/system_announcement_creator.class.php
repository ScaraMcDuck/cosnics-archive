<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../../system_announcement_repo_viewer.class.php';
require_once dirname(__FILE__).'/../../announcer/system_announcement_multipublisher.class.php';

class AdminSystemAnnouncementCreatorComponent extends AdminManagerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)), Translation :: get('SystemAnnouncements')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishSystemAnnouncement')));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($trail);
		echo $publisher;
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
	
	private function get_publisher_html()
	{		
		$object = $_GET['object'];
		$pub = new SystemAnnouncementRepoViewer($this, 'system_announcement', true);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'LearningObject: ';
			$publisher = new SystemAnnouncerMultipublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}
		
		return implode($html, "\n");
	}
}
?>