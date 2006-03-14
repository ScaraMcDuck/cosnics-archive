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
			echo '<p>Go to <a href="?linkadmin=0&amp;tool=link">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher('link', api_get_course_id(), api_get_user_id());
			$pub->display();
		}
		else
		{
			echo '<p>Go to <a href="?linkadmin=1&amp;tool=link">Publisher Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/linkbrowser.class.php';
			$browser = new LinkBrowser();
			$browser->display();
		}
	}
}
?>