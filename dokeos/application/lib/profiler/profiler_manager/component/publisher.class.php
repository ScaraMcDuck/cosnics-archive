<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../Profilercomponent.class.php';
require_once dirname(__FILE__).'/../../profilepublisher.class.php';

class ProfilerPublisherComponent extends ProfilerComponent
{	
	private $folder;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (isset($_GET[Profiler :: PARAM_FOLDER]))
		{
			$this->folder = $_GET[Profiler :: PARAM_FOLDER];
		}
		else
		{
			$this->folder = Profiler :: ACTION_FOLDER_INBOX;
		}
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('SendProfile'));
		
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