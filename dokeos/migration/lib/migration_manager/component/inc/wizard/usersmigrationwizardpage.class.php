<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../import.class.php'; 
require_once dirname(__FILE__) . '/../../../../../../users/lib/usersdatamanager.class.php'; 

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
	private $users_succes = 0;
	private $command_execute;
	
	function UsersMigrationWizardPage($command_execute)
	{
		$this->command_execute = $command_execute;
	}
	
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
		$message = $this->users_succes . ' ' . Translation :: get_lang('Users') . ' ' .
			Translation :: get_lang('migrated');
		
		if(count($this->failed_users) > 0)
			$message = $message . '<br / >' . count($this->failed_users) . ' ' .
			Translation :: get_lang('Users') . ' ' . Translation :: get_lang('failed') . '<br />';
			
		foreach($this->failed_users as $fuser)
		{
			$message = $message . '<br />' . $fuser;
		}
		
		$message = $message . '<br /><br />' . Translation :: get_lang('Dont_forget');
		
		return $message;
	}
	
	function next_step_info()
	{
		return Translation :: get_lang('System_Settings_info');
	}
	
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
	
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('users'))
		{
			echo(Translation :: get_lang('Users') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
			
			$logger->close_file();
			return false;
		}
		
		$logger->write_text('users');

		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];

		//Create logfile
		$this->logfile = new Logger('users.txt');
		$this->logfile->set_start_time();
			
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
	
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
			
		$this->mgdm->create_temporary_tables();
	
		//Migrate the users
		if(isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
		{	
			$this->migrate_users();
		}
		else
		{
			echo(Translation :: get_lang('Users') . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('users_skipped');
			return false;
		}
		
		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		$logger->close_file();
		
		return true;
	}
	
	function migrate_users()
	{
		$this->logfile->add_message('Starting migration users');
		
		$userclass = Import :: factory($this->old_system, 'user');
		$users = array();
		$users = $userclass->get_all_users($this->mgdm);

		$lcms_users = array();
		$resultset = UsersDataManager :: get_instance()->retrieve_users();
	
		while ($lcms_user = $resultset->next_result())
		{
			$lcms_users[] = $lcms_user;	
		}

		foreach($users as $user)
		{
			if($user->is_valid_user($lcms_users))
			{
				$lcms_user = $user->convert_to_new_user();
				$this->logfile->add_message('SUCCES: User added ( ID:' . $lcms_user->get_user_id() . ' )');
				$this->users_succes++;
			}
			else
			{
				$message = 'FAILED: User is not valid ( ID: ' . $user->get_user_id() . ')';
				$this->logfile->add_message($message);
				$this->failed_users[] = $message;
			}
		}
		
		$this->logfile->add_message('Users migrated');
	}

}
?>
