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
		return Translation :: get_lang('Class_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{
		$message = Translation :: get_lang('Courses_info');
		
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
			case 0: return Translation :: get_lang('Class_failed'); 
			case 1: return Translation :: get_lang('Class_User_failed'); 
			default: return Translation :: get_lang('Class_failed'); 
		}
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
		$this->logfile = new Logger('classes.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		//Migrate the classes
		$this->migrate_classes();
		
		//Migrate the class users
		//$this->migrate_class_users();
	
		//Close the logfile
		$this->logfile->write_all_messages();
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
	}
	
	/**
	 * Migrate the classes
	 */
	function migrate_classes()
	{
		$this->logfile->add_message('Starting migration classes');
		
		$class_class = Import :: factory($this->old_system, 'class');
		$classes = array();
		$classes = $class_class->get_all_classes($this->mgdm);
		
		foreach($classes as $class)
		{
			if($class->is_valid_class())
			{
				$lcms_class = $class->convert_to_new_class();
				$this->logfile->add_message('Class added ( ' . $lcms_class->get_id() . ' )');
			}
			else
			{
				$message = 'Class is not valid ( ID ' . $class->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
		}
		

		$this->logfile->add_message('Classes migrated');
	}
	
	/**
	 * Migrate the class users
	 */
	function migrate_class_users()
	{
		$this->logfile->add_message('Starting migration class users');
		
		$classuser_class = Import :: factory($this->old_system, 'classuser');
		$classusers = array();
		$classusers = $classuser_class->get_all_class_users($this->mgdm);
		
		foreach($classusers as $classuser)
		{
			if($classuser->is_valid_class_user())
			{
				$lcms_classuser = $classuser->convert_to_new_class_user();
				$this->logfile->add_message('Class user added ( ' . $lcms_classuser->get_id() . ' )');
			}
			else
			{
				$message = 'Class user is not valid ( ID ' . $classuser->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
		}
		

		$this->logfile->add_message('Classes migrated');
	}

}
?>