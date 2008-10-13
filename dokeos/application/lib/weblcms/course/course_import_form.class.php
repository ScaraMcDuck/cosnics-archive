<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_library_path().'import/import.class.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/course_category.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';

class CourseImportForm extends FormValidator {
	
	const TYPE_IMPORT = 1;
	
	private $failedcsv;
	private $udm;

    function CourseImportForm($form_type, $action) {
    	parent :: __construct('course_import', 'post', $action);
    	
		$this->form_type = $form_type;
		$this->failedcsv = array();
		if ($this->form_type == self :: TYPE_IMPORT)
		{
			$this->build_importing_form();
		}
    }
    
    function build_importing_form()
    {
    	$this->addElement('file', 'file', Translation :: get('FileName'));
		$this->addElement('submit', 'course_import', Translation :: get('Ok'));
    }
    
    function import_courses()
    {
    	$values = $this->exportValues();
    	
    	$csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
    	$failures = 0;
    	
    	foreach ($csvcourses as $csvcourse)
    	{
    		if ($this->validate_data($csvcourse))
    		{
    			$teacher_info = $this->get_teacher_info($csvcourse[Course :: PROPERTY_TITULAR]);
    			
    			$course = new Course();
    			
    			$course->set_id($csvcourse[Course :: PROPERTY_ID]);
    			$course->set_visual($csvcourse[Course :: PROPERTY_ID]);
    			$course->set_name($csvcourse[Course :: PROPERTY_NAME]);
    			$course->set_language('english');
    			$course->set_category_code($csvcourse[Course :: PROPERTY_CATEGORY_CODE]);
    			$course->set_titular($teacher_info['lastname'] . ' ' . $teacher_info['firstname']);
    			
    			if ($course->create())
    			{
    				// TODO: Temporary function pending revamped roles&rights system
    				//add_course_role_right_location_values($course->get_id());
    				$wdm = WeblcmsDataManager :: get_instance();
    				if ($wdm->subscribe_user_to_course($course, '1', '1', $teacher_info['user_id']))
    				{
    					
    				}
    				else
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
    function get_teacher_info($user_name)
    {
    	$udm = $this->udm;
    	$udm = UserDataManager :: get_instance();
    	if (!$udm->is_username_available($user_name))
    	{
    		return $udm->get_user_info($user_name);
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
    	
		//1. check if mandatory fields are set
		
		//2. check if code isn't in use
		if ($wdm->is_course($csvcourse[Course :: PROPERTY_ID]))
		{
			$failures++;
		}
		
		//3. check if teacher exists
		$teacher_info = $this->get_teacher_info($csvcourse[Course :: PROPERTY_TITULAR]);
		if (!isset($teacher_info))
		{
			$failures++;
		}
		
		//4. check if category exists
		if (!$wdm->is_course_category($csvcourse[Course :: PROPERTY_CATEGORY_CODE]))
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