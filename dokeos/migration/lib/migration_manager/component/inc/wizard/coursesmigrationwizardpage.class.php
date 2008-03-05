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
 * @author Sven Vanpoucke
 */
class CoursesMigrationWizardPage extends MigrationWizardPage
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
		return Translation :: get_lang('Courses_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{
		$message = Translation :: get_lang('Courses_info');
		
		for($i=0; $i<5; $i++)
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
			case 0: return Translation :: get_lang('Course_Category_failed'); 
			case 1: return Translation :: get_lang('Course_User_Category_failed'); 
			case 2: return Translation :: get_lang('Course_failed'); 
			case 3: return Translation :: get_lang('Course_User_Relation_failed'); 
			case 4: return Translation :: get_lang('Course_Class_Relation_failed'); 
			default: return Translation :: get_lang('Course_failed'); 
		}
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
		$exportvalues = $this->controller->exportValues();
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('courses.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		//Migrate course categories
		$this->migrate_course_categories();
		
		//Migrate the user course categories
		$this->migrate_user_course_categories();
		
		//Migrate the courses
		$this->migrate_courses();
		
		//Migrate course users
		$this->migrate_course_users();
		
		//Migrate course classes
		//$this->migrate_course_classes();
	
		//Close the logfile
		$this->logfile->write_all_messages();
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
	}
	
	/**
	 * Migrate course categories
	 */
	function migrate_course_categories()
	{
		$this->logfile->add_message('Starting migration course categories');
		
		$coursecategoryclass =  Import :: factory($this->old_system, 'coursecategory');
		$coursecategories = array();
		$coursecategories = $coursecategoryclass->get_all_course_categories($this->mgdm);
		
		foreach($coursecategories as $coursecategory)
		{
			if($coursecategory->is_valid_course_category())
			{
				$lcms_coursecategory = $coursecategory->convert_to_new_course_category();
				$this->logfile->add_message('SUCCES: Course category added ( CODE: ' . 
					$lcms_coursecategory->get_code() . ' )');
			}
			else
			{
				$message = 'FAILED: Course category is not valid ( ID: ' . $coursecategory->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
		}
		
		$this->logfile->add_message('Course categories migrated');
	}
	
	/**
	 * Migrate user course categories
	 */
	function migrate_user_course_categories()
	{
		$this->logfile->add_message('Starting migration user course categories');
		
		$usercoursecategoryclass =  Import :: factory($this->old_system, 'usercoursecategory');
		$usercoursecategories = array();
		$usercoursecategories = $usercoursecategoryclass->get_all_users_courses_categories($this->mgdm);
		
		foreach($usercoursecategories as $usercoursecategory)
		{
			if($usercoursecategory->is_valid_user_course_category())
			{
				$lcms_usercoursecategory = $usercoursecategory->convert_to_new_user_course_category();
				$this->logfile->add_message('SUCCES: User course category added ( ID: ' . 
					$lcms_usercoursecategory->get_id() . ' )');
			}
			else
			{
				$message = 'FAILED: User course category is not valid ( ID: ' . $usercoursecategory->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
		}
		
		$this->logfile->add_message('User course categories migrated');
	}
	
	/**
	 * Migrate the courses
	 */
	function migrate_courses()
	{		
		$this->logfile->add_message('Starting migration courses');
		
		$courseclass = Import :: factory($this->old_system, 'course');
		$courses = array();
		$courses = $courseclass->get_all_courses($this->mgdm);
		
		foreach($courses as $course)
		{
			if($course->is_valid_course())
			{
				$lcms_course = $course->convert_to_new_course();
				$this->logfile->add_message('SUCCES: Course added ( Course: ' . $lcms_course->get_id() . ' )');
			}
			else
			{
				$message = 'FAILED: Course is not valid ( Course: ' . $course->get_code() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[2][] = $message;
			}
		}
		

		$this->logfile->add_message('Courses migrated');
	}
	
	/**
	 * Migrate course users
	 */
	function migrate_course_users()
	{
		$this->logfile->add_message('Starting migration course users relations');
		
		$coursereluserclass = Import :: factory($this->old_system, 'coursereluser');
		$courserelusers = array();
		$courserelusers = $coursereluserclass->get_all_course_rel_user($this->mgdm);
		
		foreach($courserelusers as $coursereluser)
		{
			if($coursereluser->is_valid_course_user_relation())
			{
				$lcms_coursereluser = $coursereluser->convert_to_new_course_user_relation();
				$this->logfile->add_message('SUCCES: Course user relation added ( Course: ' 
					. $lcms_coursereluser->get_course() . ' UserID: ' .
					  $lcms_coursereluser->get_user() . ' )');
			}
			else
			{
				$message = 'FAILED: Course user relation is not valid ( Course: '
					. $coursereluser->get_course_code() . ' UserID: ' .
					  $coursereluser->get_user_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[3][] = $message;
			}
		}

		$this->logfile->add_message('Course user relations migrated');
	}
	
	/**
	 * Migrate course classes
	 */
	function migrate_course_classes()
	{
		$this->logfile->add_message('Starting migration course class relations');
		
		$courserelclass_class = Import :: factory($this->old_system, 'courserelclass');
		$courserelclasses = array();
		$courserelclasses = $courserelclass_class->get_all_course_rel_class($this->mgdm);
		
		foreach($courserelclasses as $courserelclass)
		{
			if($courserelclass->is_valid_course_class_relation())
			{
				$lcms_courserelclass = $courserelclass->convert_to_new_course_class_relation();
				$this->logfile->add_message('SUCCES: Course class relation added ( Course: ' 
					. $lcms_courserelclass->get_course() . ' UserID: ' .
					  $lcms_courserelclass->get_user() . ' )');
			}
			else
			{
				$message = 'FAILED: Course class relation is not valid ( Course: '
					. $courserelclass->get_course_code() . ' ClassID: ' .
					  $courserelclass->get_user_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[4][] = $message;
			}
		}

		$this->logfile->add_message('Course user relations migrated');
	}

}
?>