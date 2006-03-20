<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';

class LinkTool extends RepositoryTool
{
	function run()
	{
		if (isset($_GET['linkadmin']))
		{
			$_SESSION['linkadmin'] = $_GET['linkadmin'];
		}
		if ($_SESSION['linkadmin'])
		{
			echo '<p>Go to <a href="' . $this->get_url(array('linkadmin' => 0)) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'link', api_get_course_id(), api_get_user_id());
			echo $pub->as_html();
		}
		else
		{
			echo '<p>Go to <a href="' . $this->get_url(array('linkadmin' => 1)) . '">Publisher Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/linkbrowser.class.php';
			$browser = new LinkBrowser($this);
			echo $browser->as_html();
		}
	}
}
?>