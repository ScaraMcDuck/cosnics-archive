<?php
/**
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/announcementlistrenderer.class.php';
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
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			api_not_allowed();
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
			$this->display();
		}
	}

	/**
	 * Display the list of announcements
	 */
	function display()
	{
		$all_publications = $this->get_announcement_publications();
		$renderer = new AnnouncementListRenderer($this);
		$visible_publications = array();
		foreach($all_publications as $index => $publication)
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if(!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}
		echo $renderer->render($visible_publications);
	}
	/**
	 * Get the list of published announcements
	 * @return array An array with all publications of announcements
	 */
	function get_announcement_publications()
	{
		$datamanager = WebLCMSDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'announcement');
		$condition = $tool_condition;
		$announcement_publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition,false,array('display_order'),array(SORT_DESC));
		return $announcement_publications;
	}
}
?>