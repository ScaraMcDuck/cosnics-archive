<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 * 
 * This abstract class defines a page which is used in a migration wizard.
 */
abstract class MigrationWizardPage extends HTML_QuickForm_Page
{
	/**
	 * The MigrationManager component in which the wizard runs.
	 */
	private $parent;
	private $failed_elements;
	private $succes;
	private $logfile;
	private $mgdm;
	private $old_system;
	private $command_execute;
	
	/**
	 * Constructor
	 * @param string $name A unique name of this page in the wizard
	 * @param MigrationManagerComponent $parent The MigrationManager component
	 * in which the wizard runs.
	 */
	public function MigrationWizardPage($name, $parent, $command_execute = false)
	{
		$this->parent = $parent;
		parent::HTML_QuickForm_Page($name,'post');
		$this->command_execute = $command_execute;
	}
	
	/**
	 * Returns the MigrationManager component in which this wizard runs
	 * @return MigrationManager
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	/**
	 * Set the language interface of the wizard page
	 * @param string $lang A name of a language 
	 */
	function set_lang($lang)
	{
		global $language_interface;
		$language_interface = $lang;
	}
	
	/**
	 * Dummy method that classes can implement
	 */
	function perform()
	{
		
	}
	
	/**
	 * Dummy method that classes can implement
	 */
	function next_step_info()
	{
		
	}
	
	/**
	 * Get the info of a migration page
	 */
	function get_info()
	{
		for($i=0; $i<count($succes); $i++)
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
	
	/**
	 * Dummy method that some classes can implement 
	 */
	function get_message()
	{
		
	}
	
		
	/**
	 * General method for migration
	 */
	function migrate($type, $retrieve_parms = array(), $convert_parms = array(), $course = null)
	{
		$class = Import :: factory($this->old_system, strtolower($type));
		$items = array();
		
		if($course)
		{
			$this->logfile->add_message('Starting migration ' . $type . ' for course ' . $course->get_code());
			$retrieve_parms[] = $course;
			$convert_parms[] = $course;
			$final_message = $type . ' migrated for course ' . $course->get_code();
			$extra_message = ' COURSE: ' . $course;
		}
		else
		{
			$this->logfile->add_message('Starting migration ' . $type);
			$final_message = $type . ' migrated';
		}
		
		$items = $class->get_all($retrieve_parms);
		
		foreach($items as $j => $item)
		{
			if($item->is_valid($convert_parms))
			{
				$lcms_item = $item->convert_to_lcms($convert_parms);
				$this->logfile->add_message('SUCCES: ' . $type . ' added ( ID: ' . $lcms_item->get_id() . $extra_message . ' )');
				$this->succes[0]++;
				unset($lcms_item);
			}
			else
			{
				$message = 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_id() . $extra_message . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($items[$j]);
		}

		$this->logfile->add_message($final_message);
	}
	
	/** 
	 * Standard form has a next button
	 */
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	}
}

?>