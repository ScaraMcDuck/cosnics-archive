<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class AnnouncementTool extends RepositoryTool
{
	/**
	 * inherited
	 */
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
		foreach($announcement_publications as $index => $announcement_publication)
		{
			$announcement = $announcement_publication->get_learning_object();
			$html = array();
			$html[] = '<div class="learning_object">';
			$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$announcement->get_type().'.gif" alt="'.$announcement->get_type().'"/></div>';
			$html[] = '<div class="title">'.$announcement->get_title().'</div>';
			$html[] = '<div class="description">'.$announcement->get_description();
			$html[] = '<br />';
			$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/delete.gif"/>';
			$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/visible.gif"/>';
			$down_img = ($index == $number_of_announcements-1 ? 'down_na.gif' : 'down.gif');
			$up_img = ($index == 0 ? 'up_na.gif' : 'up.gif');
			$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$up_img.'"/>';
			$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$down_img.'"/>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<br /><br />';
			echo implode("\n",$html);
		}
	}
	/**
	 * Get the list of published announcements
	 * @return array An array with all publications of announcements
	 */
	function get_announcement_publications()
	{
		$datamanager = WebLCMSDataManager :: get_instance();
		$announcement_publications = $datamanager->retrieve_learning_object_publications(api_get_course_id(), null, api_get_user_id(), $this->get_groups());
		return $announcement_publications;
	}
}
?>