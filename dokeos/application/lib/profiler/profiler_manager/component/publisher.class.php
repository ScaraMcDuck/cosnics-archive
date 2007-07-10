<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profilercomponent.class.php';
require_once dirname(__FILE__).'/../../profilepublisher.class.php';

class ProfilerPublisherComponent extends ProfilerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('PublishProfile'));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($breadcrumbs);
		echo $publisher;
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
	
	private function get_publisher_html()
	{
		$pub = new ProfilePublisher($this, 'profile', true);
		$html[] =  $pub->as_html();
		
		return implode($html, "\n");
	}
}
?>