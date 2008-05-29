<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profiler_component.class.php';
require_once dirname(__FILE__).'/../../profile_publisher.class.php';

class ProfilerPublisherComponent extends ProfilerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishProfile')));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($trail);
		echo $publisher;
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
	
	private function get_publisher_html()
	{
		$types = array();
		$types[] = 'profile';
		
		$pub = new ProfilePublisher($this, $types, true);
		$html[] =  $pub->as_html();
		
		return implode($html, "\n");
	}
}
?>