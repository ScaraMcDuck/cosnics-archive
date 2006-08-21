<?php
/**
 * Dropbox tool
 * @package application.weblcms.tool
 * @subpackage dropbox
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/dropboxbrowser.class.php';
/**
 * Dropbox tool.
 */
class DropboxTool extends RepositoryTool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			$this->display_header();
			api_not_allowed();
			$this->display_footer();
			return;
		}
		if (isset($_GET['admin']))
		{
			$_SESSION['dropboxadmin'] = $_GET['admin'];
		}
		if ($_SESSION['dropboxadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'document');
			$html[] = '<p>Go to <a href="' . $this->get_url(array('admin' => 0), true) . '">User Mode</a> &hellip;</p>';
			$html[] =  $pub->as_html();
			$this->display_header();
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header();
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p>Go to <a href="' . $this->get_url(array('admin' => 1), true) . '">Publisher Mode</a> &hellip;</p>';
			}
			echo $this->perform_requested_actions();
			$browser = new DropboxBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>