<?php
/**
 * Description tool
 * @package application.weblcms.tool
 * @subpackage description
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/descriptionbrowser.class.php';
/**
 * This tool allows a user to publish descriptions in his or her course.
 */
class DescriptionTool extends RepositoryTool
{
	// Inherited.
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
			$browser = new DescriptionBrowser($this);
			echo $browser->as_html();
		}
	}
}
?>