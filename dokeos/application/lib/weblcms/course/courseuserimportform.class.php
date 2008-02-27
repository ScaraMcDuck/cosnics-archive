<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../../common/import/import.class.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/courseuserrelation.class.php';

class CourseUserImportForm extends FormValidator {
	
	const TYPE_IMPORT = 1;
	
	private $failedcsv;

    function CourseUserImportForm($form_type, $action) {
    	parent :: __construct('course_user_import', 'post', $action);
    	
		$this->form_type = $form_type;
		$this->failedcsv = array();
		if ($this->form_type == self :: TYPE_IMPORT)
		{
			$this->build_importing_form();
		}
    }
    
    function build_importing_form()
    {
    	$this->addElement('file', 'file', Translation :: get_lang('FileName'));
		$this->addElement('submit', 'course_user_import', Translation :: get_lang('Ok'));
    }
    
    function import_course_users()
    {
    	$course = $this->course;
    	$values = $this->exportValues();
    	
    	$csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
    	$failures = 0;
    	
    	foreach ($csvcourses as $csvcourse)
    	{
    		if ($this->validate_data($csvcourse))
    		{
    			$user_info = $this->get_user_info($csvcourse['username']);
    			
    			$course = new Course();
    			$course->set_id($csvcourse[CourseUserRelation :: PROPERTY_COURSE]);
    			
    			$wdm = WeblcmsDataManager :: get_instance();
    			if (!$wdm->subscribe_user_to_course($course, $csvcourse[CourseUserRelation :: PROPERTY_STATUS], ($csvcourse[CourseUserRelation :: PROPERTY_STATUS] == 1 ? 1 : 5), $user_info['user_id']))
    			{
    				$failures++;
    				$this->failedcsv[] = implode($csvcourse, ';');
    			}
    		}
    		else
    		{
    			$failures++;
    			$this->failedcsv[] = implode($csvcourse, ';');
    		}
    	}
    	
    	if ($failures > 0)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    // TODO: Temporary solution pending implementation of user object
    function get_user_info($user_name)
    {
    	if (!UserManager :: is_username_available($user_name))
    	{
    		return UserManager :: get_user_info($user_name);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    function get_failed_csv()
    {
    	return implode($this->failedcsv, '<br />');
    }
    
    function validate_data($csvcourse)
    {
    	$failures = 0;
    	$wdm = WeblcmsDataManager :: get_instance();
		
		//1. check if user exists
		// TODO: Change to appropriate property once the user-class is operational
		$user_info = $this->get_user_info($csvcourse['username']);
		if (!isset($user_info))
		{
			$failures++;
		}
		
		//2. check if course code exists
		if (!$wdm->is_course($csvcourse[CourseUserRelation :: PROPERTY_COURSE]))
		{
			$failures++;
		}
		
		//3. Status valid ?
		if ($csvcourse[CourseUserRelation :: PROPERTY_STATUS] != 1 && $csvcourse[CourseUserRelation :: PROPERTY_STATUS] != 5)
		{
			$failures++;
		}
		
		if ($failures > 0)
		{
			return false;
		}
		else
		{
    		return true;
		}
    }
}
?>