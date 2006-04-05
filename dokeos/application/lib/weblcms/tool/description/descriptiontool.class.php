<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
/**
 * This tool allows a user to publish descriptions in his or her course.
 */
class DescriptionTool extends RepositoryTool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		if (isset($_GET['descriptionadmin']))
		{
			$_SESSION['descriptionadmin'] = $_GET['descriptionadmin'];
		}
		if ($_SESSION['descriptionadmin'])
		{
			echo '<p>Go to <a href="' . $this->get_url(array('descriptionadmin' => 0), true) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'description');
			echo $pub->as_html();
		}
		else
		{
			echo '<p>Go to <a href="' . $this->get_url(array('descriptionadmin' => 1), true) . '">Publisher Mode</a> &hellip;</p>';
			$this->perform_requested_actions();
			$this->display();
		}
	}
	/**
	 * Display the list of announcements
	 */
	function display()
	{
		$publications = $this->get_publications();
		$number_of_publications = count($publications);
		foreach($publications as $index => $publication)
		{
			$object = $publication->get_learning_object();
			$target_users = $publication->get_target_users();
			$delete_url = $this->get_url(array('action'=>'delete','pid'=>$publication->get_id()), true);
			$visible_url = $this->get_url(array('action'=>'toggle_visibility','pid'=>$publication->get_id()), true);

			if($index != 0)
			{
				$up_img = 'up.gif';
				$up_url = $this->get_url(array('action'=>'move_up','pid'=>$publication->get_id()), true);
				$up_link = '<a href="'.$up_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$up_img.'"/></a>';
			}
			else
			{
				$up_link = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/up_na.gif"/></a>';
			}
			if($index != $number_of_publications-1)
			{
				$down_img = 'down.gif';
				$down_url = $this->get_url(array('action'=>'move_down','pid'=>$publication->get_id()), true);
				$down_link = '<a href="'.$down_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$down_img.'"/></a>';
			}
			else
			{
				$down_link = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/down_na.gif"/></a>';
			}
			$visibility_img = ($publication->is_hidden() ? 'invisible.gif' : 'visible.gif');

			$html = array();
			$html[] = '<div class="learning_object">';
			$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
			$html[] = '<div class="title">'.htmlentities($object->get_title()).'</div>';
			$html[] = '<div class="description">'.$object->get_description();
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
	function get_publications()
	{
		$datamanager = WebLCMSDataManager :: get_instance();
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'description');
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $publications;
	}
}
?>