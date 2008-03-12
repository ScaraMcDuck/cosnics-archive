<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../import.class.php'; 
/**
 * Class for class migration execution
 * @author Sven Vanpoucke
 */
class PersonalAgendasMigrationWizardPage extends MigrationWizardPage
{
	private $logfile;
	private $mgdm;
	private $old_system;
	private $failed_elements;
	private $succes;
	private $command_execute;
	
	function PersonalAgendasMigrationWizardPage($command_execute)
	{
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Personal_agenda_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<1; $i++)
		{
			$message = $message . '<br />' . $this->succes[$i] . ' ' . $this->get_message($i) . ' ' .
				Translation :: get_lang('migrated');
			
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / >' . count($this->failed_elements[$i]) . ' ' .
					 $this->get_message($i) . ' ' . Translation :: get_lang('failed');
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement ;
			}
			
			$message = $message . '<br />';
		}
		
		$message = $message . '<br />' . Translation :: get_lang('Dont_forget');
		
		return $message;
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Personal_agendas'); 
			default: return Translation :: get_lang('Personal_agendas'); 
		}
	}
	
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	}
	
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('personalagendas'))
		{
			echo(Translation :: get_lang('Personal_agendas') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('personalagendas');
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('personalagenda.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['migrate_personal_agendas']) && $exportvalues['migrate_personal_agendas'] == 1)
		{	
			//Migrate the personal agendas
			if(isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
			{
				$this->migrate_personal_agendas();
			}
			else
			{
				echo(Translation :: get_lang('Personal_agendas') . ' ' .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Personal agendas failed because users skipped');
				$this->succes[0] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Personal_agendas')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('personal agendas skipped');
			
			return false;
		}
	
		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		return true;
	}
	
	/**
	 * Migrate the classes
	 */
	function migrate_personal_agendas()
	{
		$this->logfile->add_message('Starting migration personal agendas');
		
		$pa_class = Import :: factory($this->old_system, 'personalagenda');
		$pas = array();
		$pas = $pa_class->get_all_personal_agendas($this->mgdm);
		
		foreach($pas as $pa)
		{
			if($pa->is_valid_personal_agenda())
			{
				$lcms_pa = $pa->convert_to_new_personal_agenda();
				$this->logfile->add_message('SUCCES: Personal Agenda added ( ' . $lcms_pa->get_id() . ' )');
				$this->succes[0]++;
			}
			else
			{
				$message = 'FAILED: Personal Agenda is not valid ( ID ' . $pa->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
		}
		

		$this->logfile->add_message('Personal agendas migrated');
	}

}
?>