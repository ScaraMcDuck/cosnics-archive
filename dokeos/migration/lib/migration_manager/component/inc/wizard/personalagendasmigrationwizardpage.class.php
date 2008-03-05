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
class ClassesMigrationWizardPage extends MigrationWizardPage
{
	private $logfile;
	private $mgdm;
	private $old_system;
	private $failed_elements;
	
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
		$message = Translation :: get_lang('Personal_agenda_info');
		
		for($i=0; $i<2; $i++)
		{
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / ><br />' . 
					$this->get_failed_message($i) . ' (' .
					Translation :: get_lang('Dont_forget') . ')';
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement;
			}
		}
		
		return $message;
	}
	
	function get_failed_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Personal_agenda_failed'); 
			default: return Translation :: get_lang('Personal_agenda_failed'); 
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
		$exportvalues = $this->controller->exportValues();
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('classes.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		//Migrate the personal agendas
		$this->migrate_personal_agendas();
		
	
		//Close the logfile
		$this->logfile->write_all_messages();
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
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