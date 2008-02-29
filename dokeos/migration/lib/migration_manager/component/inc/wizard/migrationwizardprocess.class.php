<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
 
 require_once(dirname(__FILE__) . '/../../../../import.class.php');
 require_once(dirname(__FILE__) . '/../../../../logger.class.php');
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
		
		$mgdm = MigrationDataManager :: getInstance($exportvalues['old_system']);
		$mgdm->create_temporary_tables();
		
		echo(Translation :: get_lang('migrating') . ' ' . 
			 Translation :: get_lang('users') . '<br />');
			 
		$logfile = new Logger('user');
		$logfile->set_start_time();
		$logfile->add_message('started migrating users');
		$userclass = Import :: factory($exportvalues['old_system'], 'user');
		$users = array();
		$users = $userclass->get_all_users();
		
		foreach($users as $user)
		{
			$lcms_user = $user->convert_to_new_user();
		}
		

		$logfile->add_message('users migrated');
		echo( Translation :: get_lang('users') . ' ' .
			  Translation :: get_lang('done') . '<br />');
			  
		$logfile->write_passed_time();
		$this->parent->display_footer();
	}
}
