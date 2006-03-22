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
			$pub = new LearningObjectPublisher($this, 'announcement');
			echo $pub->as_html();
		}
		else
		{
			echo '<p>Go to <a href="' . $this->get_url(array('announcementadmin' => 1)) . '">Publisher Mode</a> &hellip;</p>';
			$this->perform_requested_actions();
			$this->display();
		}
	}
	/**
	 * Handles requests like deleting a publication, changing display order of
	 * publication, etc.
	 * The action and the necessary parameters are retrieved from the query
	 * string
	 */
	 // TODO: move this function to parent class so it can be shared with other tools
	 // TODO: add some input validation to check if the requested action can be performed
	function perform_requested_actions()
	{
		if(isset($_GET['action']))
		{
			$datamanager = WebLCMSDataManager :: get_instance();
			switch($_GET['action'])
			{
				case 'delete':
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					if($publication->delete())
					{
						$message = get_lang('LearningObjectPublicationDeleted');
					}
					break;
				case 'change_visibility':
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					$publication->change_visibility();
					if($publication->update())
					{
						$message = get_lang('LearningObjectPublicationVisibilityChanged');
					}
					break;
				case 'move_up':
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					if($publication->move_up())
					{
						$message = get_lang('LearningObjectPublicationMovedUp');
					}
					break;
				case 'move_down':
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					if($publication->move_down())
					{
						$message = get_lang('LearningObjectPublicationMovedDown');
					}
					break;
			}
		}
		if( isset($message))
		{
			Display::display_normal_message($message);
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
			$target_users = $announcement_publication->get_target_users();
			$delete_url = $this->get_url(array('action'=>'delete','pid'=>$announcement_publication->get_id()));
			$visible_url = $this->get_url(array('action'=>'change_visibility','pid'=>$announcement_publication->get_id()));

			if($index != 0)
			{
				$up_img = 'up.gif';
				$up_url = $this->get_url(array('action'=>'move_up','pid'=>$announcement_publication->get_id()));
				$up_link = '<a href="'.$up_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$up_img.'"/></a>';
			}
			else
			{
				$up_link = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/up_na.gif"/></a>';
			}
			if($index != $number_of_announcements-1)
			{
				$down_img = 'down.gif';
				$down_url = $this->get_url(array('action'=>'move_down','pid'=>$announcement_publication->get_id()));
				$down_link = '<a href="'.$down_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$down_img.'"/></a>';
			}
			else
			{
				$down_link = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/down_na.gif"/></a>';
			}
			$visibility_img = ($announcement_publication->is_hidden() ? 'visible.gif' : 'invisible.gif');

			$html = array();
			$html[] = '<div class="learning_object">';
			$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$announcement->get_type().'.gif" alt="'.$announcement->get_type().'"/></div>';
			$html[] = '<div class="title">'.$announcement->get_title().'</div>';
			$html[] = '<div class="description">'.$announcement->get_description();
			$html[] = '<br />';
			$html[] = '<a href="'.$delete_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/delete.gif"/></a>';
			$html[] = '<a href="'.$visible_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$visibility_img.'"/></a>';
			$html[] = $up_link;
			$html[] = $down_link;
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
		$announcement_publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups());
		return $announcement_publications;
	}
}
?>