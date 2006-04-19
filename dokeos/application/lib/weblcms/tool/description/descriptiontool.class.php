<?php
/**
 * Description tool
 * @package application.weblcms.tool
 * @subpackage description
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
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
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			api_not_allowed();
			return;
		}
		if (isset($_GET['descriptionadmin']))
		{
			$_SESSION['descriptionadmin'] = $_GET['descriptionadmin'];
		}
		if ($_SESSION['descriptionadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			echo '<p>Go to <a href="' . $this->get_url(array('descriptionadmin' => 0), true) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'description');
			echo $pub->as_html();
		}
		else
		{
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p>Go to <a href="' . $this->get_url(array('descriptionadmin' => 1), true) . '">Publisher Mode</a> &hellip;</p>';
			}
			$this->perform_requested_actions();
			$this->display();
		}
	}

	/**
	 * Display the list of descriptions
	 */
	function display()
	{
		$all_publications = $this->get_description_publications();
		$renderer = new LearningObjectPublicationListRenderer($this);
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
	 * Get the list of published descriptions
	 * @return array An array with all publications of descriptions
	 */
	function get_description_publications()
	{
		$datamanager = WebLCMSDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'description');
		$condition = $tool_condition;
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $publications;
	}
}
?>