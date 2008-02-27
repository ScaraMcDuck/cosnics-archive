<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
 
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
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
		$this->parent->display_header();
		echo('test');
		$this->parent->display_footer();
	}
}
