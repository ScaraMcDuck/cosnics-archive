<?php
/**
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/announcementbrowser.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class AnnouncementTool extends RepositoryTool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		$this->display_header();
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			api_not_allowed();
			$this->display_footer();
			return;
		}
		if (isset($_GET['announcementadmin']))
		{
			$_SESSION['announcementadmin'] = $_GET['announcementadmin'];
		}
		if ($_SESSION['announcementadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			echo '<p>Go to <a href="' . $this->get_url(array('announcementadmin' => 0), true) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'announcement');
			echo $pub->as_html();
		}
		else
		{
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p>Go to <a href="' . $this->get_url(array('announcementadmin' => 1), true) . '">Publisher Mode</a> &hellip;</p>';
			}
			$this->perform_requested_actions();
			$browser = new AnnouncementBrowser($this);
			echo $browser->as_html();
		}
		$this->display_footer();
	}
}
?>