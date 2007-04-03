<?php
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../../main/inc/lib/import.lib.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/coursecategory.class.php';

class CourseImportForm extends FormValidator {
	
	const TYPE_IMPORT = 1;
	
	private $parent;

    function CourseImportForm($form_type, $action) {
    	parent :: __construct('course_import', 'post', $action);
    	
		$this->form_type = $form_type;
		if ($this->form_type == self :: TYPE_IMPORT)
		{
			$this->build_importing_form();
		}
    }
    
    function build_importing_form()
    {
    	$this->addElement('file', 'file', get_lang('FileName'));
		$this->addElement('submit', 'course_import', get_lang('Ok'));
    }
    
    function import_courses()
    {
    	$course = $this->course;
    	$values = $this->exportValues();
    	
    	$csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
    	$failures = 0;
    	
    	foreach ($csvcourses as $csvcourse)
    	{
    		$course = new Course();
    		
    		$course->set_id($csvcourse[Course :: PROPERTY_ID]);
    		$course->set_visual($csvcourse[Course :: PROPERTY_ID]);
    		$course->set_name($csvcourse[Course :: PROPERTY_NAME]);
    		$course->set_category_code($csvcourse[Course :: PROPERTY_CATEGORY_CODE]);
    		$course->set_titular($csvcourse[Course :: PROPERTY_TITULAR]);
    		
    		if (!$course->create())
    		{
    			$failures++;
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
}
?>