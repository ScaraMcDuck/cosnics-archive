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
	private $failed_course_categories;
	private $failed_courses;
	private $failed_course_rel_user;
	
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
		
		if(count($this->failed_course_categories) > 0)
			$message = $message . '<br / ><br />' . 
				Translation :: get_lang('Course_Categories_failed') . ' (' .
				Translation :: get_lang('Dont_forget') . ')';
			
		foreach($this->failed_course_categories as $fcoursecategory)
		{
			$message = $message . '<br />' . $fcoursecategory;
		}
		
		if(count($this->failed_courses) > 0)
			$message = $message . '<br / ><br />' . 
				Translation :: get_lang('Course_failed')  . ' (' .
				Translation :: get_lang('Dont_forget') . ')';
			
		foreach($this->failed_courses as $fcourses)
		{
			$message = $message . '<br />' . $fcourses;
		}
		
		if(count($this->failed_course_rel_user) > 0)
			$message = $message . '<br / ><br />' . 
				Translation :: get_lang('Course_User_Relation_failed')  . ' (' .
				Translation :: get_lang('Dont_forget') . ')';
			
		foreach($this->failed_course_rel_user as $fcourserelusers)
		{
			$message = $message . '<br />' . $fcourserelusers;
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
				$this->logfile->add_message('Course category added ( ' . 
					$lcms_coursecategory->get_code() . ' )');
			}
			else
			{
				$message = 'Course category is not valid ( ID ' . $coursecategory->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_course_categories[] = $message;
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
				$this->logfile->add_message('User course category added ( ' . 
					$lcms_usercoursecategory->get_id() . ' )');
			}
			else
			{
				$message = 'User course category is not valid ( ID ' . $usercoursecategory->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_course_categories[] = $message;
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
				$this->logfile->add_message('Course added ( ' . $lcms_course->get_id() . ' )');
			}
			else
			{
				$message = 'Course is not valid ( ' . $course->get_code() . ' )';
				$this->logfile->add_message($message);
				$this->failed_courses[] = $message;
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
				$this->logfile->add_message('Course user relation added ( ' 
					. $lcms_coursereluser->get_course_code() . ' ' .
					  $lcms_coursereluser->get_user_id() . ' )');
			}
			else
			{
				$message = 'Course user relation is not valid ( '
					. $coursereluser->get_course_code() . ' ' .
					  $coursereluser->get_user_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_course_rel_user[] = $message;
			}
		}

		$this->logfile->add_message('Course user relations migrated');
	}

}
?>