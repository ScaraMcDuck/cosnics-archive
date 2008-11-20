<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
 require_once Path :: get_library_path().'filesystem/filesystem.class.php';
 require_once Path :: get_application_path().'lib/application.class.php';
 require_once Path :: get_library_path().'installer.class.php';
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class PublisherWizardProcess extends HTML_QuickForm_Action
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	/**
	 * Constructor
	 * @param Tool $parent The repository tool in which the wizard
	 * runs.
	 */
	public function PublisherWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	function perform($page, $actionName)
	{
		$values = $page->controller->exportValues();
		
		// Display the page header
		$this->parent->display_header();
		
		$previous_application = '';
		$message = '';
		
		$ids = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if(!is_array($ids))
			$ids = array($ids);
		
		foreach($ids as $id)
			$los[] = $this->parent->retrieve_learning_object($id);
		
		foreach($values as $location => $value)
		{
			if($value == 1)
			{
				$split = split('_', $location);
				$application_name = $split[0];
				if($application_name != $previous_application)
				{
					if($previous_application != '')
						$this->process_result($previous_application, true, $message);
					$message = '';
					$previous_application = $application_name;
				}
				
				unset($split[0]);
				$location = implode('_', $split);
				$application = Application::factory($application_name);
				foreach($los as $lo)
					$message .= $application->publish_learning_object($lo, $location) . '<br />';
			}
		}
		
		$this->process_result($previous_application, true, $message);
		
		// Display the page footer
		$this->parent->display_footer();
	}

	
	function display_block_header($application)
	{
		$html = array();
		$html[] = '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(layout/aqua/img/admin/place_'. DokeosUtilities :: camelcase_to_underscores($application) .'.png);">';
		$html[] = '<div class="title">'. Translation :: get(Application::application_to_class($application)) .'</div>';
		$html[] = '<div class="description">';
		return implode("\n", $html);
	}
	
	function display_block_footer()
	{
		$html = array();
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	function process_result($application, $result, $message)
	{
		echo $this->display_block_header($application);
		echo $message;
		echo $this->display_block_footer();
		if (!$result) 
		{
			$this->parent->display_footer();
			exit;
		}
		
	}
}
?>