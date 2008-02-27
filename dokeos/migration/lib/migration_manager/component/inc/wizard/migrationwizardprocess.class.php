<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
 
 require_once(dirname(__FILE__) . '/../../../../import.class.php');
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 * 
 * @author Sven Vanpoucke
 */
class MigrationWizardProcess extends HTML_QuickForm_Action
{
	/**
	 * The migration component in which the wizard runs.
	 */
	private $parent;
	
	/**
	 * Constructor
	 * @param MigrationComponent $parent The migration component in which the wizard
	 * runs.
	 */
	public function MigrationWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	
	function perform($page, $actionName)
	{
		$exportvalues = $page->controller->exportValues();
		$this->parent->display_header();
		
		echo('Migrating users<br />');
		$userclass = Import :: factory($exportvalues['old_system'], 'user');
		$users = array();
		$users = $userclass->getAllUsers();
		
		foreach($users as $user)
		{
			$lcms_user = $user->convertToNewUser();
		}
		
		echo('All users done<br />');
		
		$this->parent->display_footer();
	}
}
