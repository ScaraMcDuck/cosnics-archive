<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';

class AnnouncementTool extends RepositoryTool
{
	function run()
	{
		if (isset($_GET['announcementadmin']))
		{
			$_SESSION['announcementadmin'] = $_GET['announcementadmin'];
		}
		if ($_SESSION['announcementadmin'])
		{
			echo '<p>Go to <a href="' . $this->get_url(array('announcementadmin' => 0)) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'announcement', api_get_course_id(), api_get_user_id());
			echo $pub->as_html();
		}
		else
		{
			echo '<p>Go to <a href="' . $this->get_url(array('announcementadmin' => 1)) . '">Publisher Mode</a> &hellip;</p>';
			//TODO: code to display published announcements
		}
	}
}
?>