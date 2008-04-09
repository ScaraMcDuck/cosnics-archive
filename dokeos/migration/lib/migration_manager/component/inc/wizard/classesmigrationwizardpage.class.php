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
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	//private $failed_elements;
	//private $succes;
	//private $command_execute;
	
	/**
	 * Constructor creates a new ClassesMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function ClassesMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('Class_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<2; $i++)
		{
			$message = $message . '<br />' . $this->succes[$i] . ' ' . $this->get_message($i) . ' ' .
				Translation :: get('migrated');
			
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / >' . count($this->failed_elements[$i]) . ' ' .
					 $this->get_message($i) . ' ' . Translation :: get('failed');
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement ;
			}
			
			$message = $message . '<br />';
		}
		
		$message = $message . '<br />' . Translation :: get('Dont_forget');
		
		return $message;
	}
	
	/**
	 * Retrieves the next step info
	 * @return string Info about the next step
	 */
	function next_step_info()
	{
		return Translation :: get('Courses_info');
	}
	
	/**
	 * Retrieves the correct message for the correct index, this is used in cooperation with
	 * $failed elements and the method getinfo 
	 * @param int $index place in $failedelements for which the message must be retrieved
	 */
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get('Classes'); 
			case 1: return Translation :: get('Class_users'); 
			default: return Translation :: get('Classes'); 
		}
	}
	
	/**
	 * Builds the next button
	 */
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	}
	
	/**
	 * Execute the page
	 * Starts migration for classes and class users
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('classes'))
		{
			echo(Translation :: get('Classes') . ' ' .
				 Translation :: get('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('classes');
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
		
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('classes.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['migrate_classes']) && $exportvalues['migrate_classes'] == 1)
		{	
			//Migrate the classes
			$this->migrate_classes();
			
			//Migrate the class users
			if(isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
			{
				$this->migrate_class_users();
			}
			else
			{
				echo(Translation :: get('Class_users') . ' ' .
				     Translation :: get('failed') . ' ' .
				     Translation :: get('because') . ' ' . 
				     Translation :: get('Users') . ' ' .
				     Translation :: get('skipped') . '<br />');
				$this->logfile->add_message('Classes failed because users skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get('Classes')
				 . ' ' . Translation :: get('skipped') . '<br />');
			$this->logfile->add_message('Classes skipped');
			
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
	function migrate_classes()
	{
		$this->logfile->add_message('Starting migration classes');
		
		$class_class = Import :: factory($this->old_system, 'class');
		$classes = array();
		$classes = $class_class->get_all(array('mgdm' => $this->mgdm));
		
		foreach($classes as $i => $class)
		{
			if($class->is_valid_class())
			{
				$lcms_class = $class->convert_to_new_class();
				$this->logfile->add_message('SUCCES: Class added ( ' . $lcms_class->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_class);
			}
			else
			{
				$message = 'FAILED: Class is not valid ( ID ' . $class->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($classes[$i]);
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
		$classusers = $classuser_class->get_all(array('mgdm' => $this->mgdm));
		
		foreach($classusers as $i => $classuser)
		{
			if($classuser->is_valid_class_user())
			{
				$lcms_classuser = $classuser->convert_to_new_class_user();
				$this->logfile->add_message('SUCCES: Class user added ( Class: ' . 
				$lcms_classuser->get_classgroup_id() . ' User: ' . 
				$lcms_classuser->get_user_id() . ' )');
				$this->succes[1]++;
				unset($lcms_classuser);
			}
			else
			{
				$message = 'FAILED: Class user is not valid ( Class: ' . 
				$classuser->get_class_id() . ' User: ' .
				$classuser->get_user_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
			
			unset($classusers[$i]);
		}
		

		$this->logfile->add_message('Classes migrated');
	}

}
?>