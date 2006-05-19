<?php
/**
 * Forum tool
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/forumbrowser.class.php';
/**
 * This tool allows a user to publish forums in his or her course.
 */
class ForumTool extends RepositoryTool
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
		if (isset($_GET['forumadmin']))
		{
			$_SESSION['forumadmin'] = $_GET['forumadmin'];
		}
		if ($_SESSION['forumadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			echo '<p>Go to <a href="' . $this->get_url(array('forumadmin' => 0), true) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'forum');
			echo $pub->as_html();
		}
		else
		{
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p>Go to <a href="' . $this->get_url(array('forumadmin' => 1), true) . '">Publisher Mode</a> &hellip;</p>';
			}
			$this->perform_requested_actions();
			$browser = new ForumBrowser($this);
			echo $browser->as_html();
		}
		$this->display_footer();
	}
}
?>