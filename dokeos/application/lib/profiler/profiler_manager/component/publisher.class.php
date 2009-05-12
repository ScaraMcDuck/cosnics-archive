<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profiler_component.class.php';
require_once dirname(__FILE__).'/../../publisher/profile_publisher.class.php';
require_once dirname(__FILE__).'/../../profile_repo_viewer.class.php';

class ProfilerPublisherComponent extends ProfilerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Profiler::PARAM_ACTION => Profiler::ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishProfile')));
		
		$object = $_GET['object'];
		$pub = new ProfileRepoViewer($this, 'profile', true);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url(array('go' => null), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'LearningObject: ';
			$publisher = new ProfilePublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}
		
		$this->display_header($trail);
		echo implode("\n", $html);
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
}
?>