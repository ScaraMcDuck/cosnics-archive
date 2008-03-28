<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
/**
 * Class for course documents migration
 * @author Van Wayenbergh David
 */
class DocumentsMigrationWizardPage extends MigrationWizardPage
{
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	//private $failed_elements;
	private $include_deleted_files;
	//private $succes;
	//private $command_execute;
	
	function DocumentsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Documents_title');
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
	
	function next_step_info()
	{
		return Translation :: get_lang('Documents_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Documents'); 
			default: return Translation :: get_lang('Documents'); 
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
		
		if($logger->is_text_in_file('documents'))
		{
			echo(Translation :: get_lang('Documents') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
			return false;
		}
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		$this->include_deleted_files = $exportvalues['migrate_deleted_files'];
		
		//Create logfile
		$this->logfile = new Logger('documents.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		$csvlogger = new Logger('doc.csv');
		$csvlogger->write_text('oud document pad;filesize;total passed time;copytime;documenttime;categories_time;publication_time;' .
			'idref_time;orphan_time;doublefile_time;totaltime (copy to pub);difference (passed - total)');
		$csvlogger->close_file();
		
		if(isset($exportvalues['migrate_documents']) && $exportvalues['migrate_documents'] == 1)
		{	
			//Migrate the calendar events and resources
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
					 $exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				$courseclass = Import :: factory($this->old_system, 'course');
				$courses = array();
				$courses = $courseclass->get_all(array('mgdm' => $this->mgdm));
				
				foreach($courses as $i => $course)
				{
					$old_rel_path = 'courses/' . $course->get_code() . '/document/';
					$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);
					$full_path = $this->mgdm->append_full_path(false,$old_rel_path);
					
					if ($this->mgdm->get_failed_element('dokeos_main.course', $course->get_code()) || !is_dir($full_path))
					{
						continue;
					}	
			
					$this->migrate_documents($course);
					unset($courses[$i]);
					flush();
				}
			}
			else
			{
				echo(Translation :: get_lang('Documents') . ' ' .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Calendar events failed because users skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Documents')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Documents skipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		$logger->write_text('documents');
		$logger->close_file();
		return true;
	}
	
	/**
	 * Migrate the calendar events
	 */
	function migrate_documents($course)
	{
		$this->logfile->add_message('Starting migration documents for course ' . $course->get_code());
		
		$csvlogger = new Logger('doc.csv', true);
		
		$class_document = Import :: factory($this->old_system, 'document');
		$documents = array();
		$documents = $class_document->get_all_documents($course, $this->mgdm, $this->include_deleted_files);
		
		foreach($documents as $j => $document)
		{
			if($document->is_valid_document($course))
			{
				$begin_time = Logger :: get_microtime();		
				$array = $document->convert_to_new_document($course);
				$lcms_document = $array['document'];
				
				if($lcms_document)
				{
					$end_time = Logger :: get_microtime();
					$passedtime = ($end_time - $begin_time);
					
					$this->logfile->add_message('SUCCES: document added ( ID: ' . $lcms_document->get_id() . ' )');
					$copytime = $array['copy_time'];
					$documenttime = $array['document_time'];
					$categories_time = $array['categories_time'];
					$publication_time = $array['publication_time'];
					$idref_time = $array['idref_time'];
					$orphan_time = $array['orphan_time'];
					$doublefile_time = $array['doublefile_time'];
					$total_time = $copytime + $documenttime + $publication_time + $idref_time + $orphan_time + $doublefile_time;
					$difference = $passedtime - $total_time;
					
					$passedtime = number_format($passedtime, 3, ',', '');
					$copytime = number_format($copytime, 3, ',', '');
					$documenttime = number_format($documenttime, 3, ',', '');
					$categories_time = number_format($categories_time, 3, ',', '');
					$publication_time = number_format($publication_time, 3, ',', '');
					$idref_time = number_format($idref_time, 3, ',', '');
					$orphan_time = number_format($orphan_time, 3, ',', '');
					$doublefile_time = number_format($doublefile_time, 3, ',', '');
					$total_time = number_format($total_time, 3, ',', '');
					$difference = number_format($difference, 3, ',', '');
					
					$csvlogger->write_text($document->get_path() . ';' . $lcms_document->get_filesize() .
						';' . $passedtime . ';' . $copytime . ';' . $documenttime . ';' . $categories_time .
						';' . $publication_time . ';' . $idref_time . ';' . $orphan_time . 
						';' . $doublefile_time . ';' . $total_time . ';' . $difference);
				}

				$this->succes[0]++;
				unset($lcms_document);
			}
			else
			{
				$message = 'FAILED: Document is not valid ( ID ' . $document->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($documents[$j]);
		}
		
		$csvlogger->close_file();

		$this->logfile->add_message('Documents migrated for course ' . $course->get_code());
	}

}
?>
