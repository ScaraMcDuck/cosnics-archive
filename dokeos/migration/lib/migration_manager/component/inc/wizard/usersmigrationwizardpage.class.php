<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../import.class.php'; 
/**
 * Class for user migration execution
 * 
 */
class UsersMigrationWizardPage extends MigrationWizardPage
{
	private $logfile;
	private $mgdm;
	private $old_system;
	private $failed_users = array();
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Users_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{
		$message = Translation :: get_lang('Users_info');
		
		if(count($this->failed_users) > 0)
			$message = $message . '<br / ><br />' . 
				Translation :: get_lang('Users_failed')  . ' (' .
				Translation :: get_lang('Dont_forget') . ')';
			
		foreach($this->failed_users as $fuser)
		{
			$message = $message . '<br />' . $fuser;
		}
		
		return $message;
	}
	
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->perform();
	}
	
	function perform()
	{
		$exportvalues = $this->controller->exportValues();
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('users.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		$this->mgdm->create_temporary_tables();
		
		//Migrate the users
		$this->migrate_users();
	
		//Close the logfile
		$this->logfile->write_all_messages();
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
	}
	
	function migrate_users()
	{
		$this->logfile->add_message('Starting migration users');
		
		$userclass = Import :: factory($this->old_system, 'user');
		$users = array();
		$users = $userclass->get_all_users($this->mgdm);
		
		foreach($users as $user)
		{
			if($user->is_valid_user())
			{
				$lcms_user = $user->convert_to_new_user();
				$this->logfile->add_message('User added ( ' . $lcms_user->get_user_id() . ' )');
			}
			else
			{
				$message = 'User is not valid ( ID ' . $user->get_user_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_users[] = $message;
			}
		}
		

		$this->logfile->add_message('Users migrated');
	}

}
?>
