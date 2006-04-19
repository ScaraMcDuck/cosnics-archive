<?php
/**
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
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
		$announcement_publications = $this->get_announcement_publications();
		$number_of_announcements = count($announcement_publications);
		$renderer = new LearningObjectPublicationListRenderer($this);
		foreach($announcement_publications as $index => $announcement_publication)
		{
			// If the announcement is hidden and the user is not allowed to DELETE or EDIT, don't show this announcement
			if(!$announcement_publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			echo $renderer->render($announcement_publication);
		}
	}
	/**
	 * Get the list of published announcements
	 * @return array An array with all publications of announcements
	 */
	function get_announcement_publications()
	{
		$datamanager = WebLCMSDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'announcement');
		//$from_date_condition = new InequalityCondition(LearningObjectPublication :: PROPERTY_FROM_DATE,InequalityCondition::LESS_THAN_OR_EQUAL,time());
		//$to_date_condition = new InequalityCondition(LearningObjectPublication :: PROPERTY_TO_DATE,InequalityCondition::GREATER_THAN_OR_EQUAL,time());
		//$publication_period_cond = new AndCondition($from_date_condition,$to_date_condition);
		//$forever_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_FROM_DATE,0);
		//$time_condition = new OrCondition($publication_period_cond,$forever_condition);
		//$condition = new AndCondition($tool_condition,$time_condition);
		$condition = $tool_condition;
		$announcement_publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $announcement_publications;
	}
}
?>