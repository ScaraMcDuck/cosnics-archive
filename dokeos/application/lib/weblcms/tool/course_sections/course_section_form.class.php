<?php
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../course/course_section.class.php';

class CourseSectionForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'CourseSettingUpdated';
	const RESULT_ERROR = 'CourseSettingUpdateFailed';

	private $course_section;
	private $form_type;

    function CourseSectionForm($form_type, $course_section, $action) 
    {
    	parent :: __construct('course_sections', 'post', $action);
   
    	$this->course_section = $course_section;
		$this->form_type = $form_type;
		
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{ 
			$this->build_creation_form();
		}
		
		$this->setDefaults();
    }
    
    function build_basic_form()
    { 
		$this->addElement('text', CourseSection :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(CourseSection :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('checkbox', CourseSection :: PROPERTY_VISIBLE, Translation :: get('Visible'));
		$this->addElement('submit', 'course_section_sections', 'OK');
    }
    
    function build_editing_form()
    {
    	$course_section = $this->course_section;
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', CourseSection :: PROPERTY_ID);
    }
    
    function build_creation_form()
    {		
    	$this->build_basic_form();
    }
    
    function update_course_section()
    {
    	$course_section = $this->course_section;
    	$values = $this->exportValues();
    	
    	$course_section->set_name($values[CourseSection :: PROPERTY_NAME]);
    	$visible = $values[CourseSection :: PROPERTY_VISIBLE]?$values[CourseSection :: PROPERTY_VISIBLE]:0;
    	$course_section->set_visible($visible);
    	
   		return $course_section->update();
    }
    
    
    
    function create_course_section()
    {
    	$course_section = $this->course_section;
    	$values = $this->exportValues();
    	
    	$course_section->set_name($values[CourseSection :: PROPERTY_NAME]);
    	$visible = $values[CourseSection :: PROPERTY_VISIBLE]?$values[CourseSection :: PROPERTY_VISIBLE]:0;
    	$course_section->set_visible($visible);
    	
   		return $course_section->create();
    }
    
	/**
	 * Sets default values. 
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$course_section = $this->course_section;
		$defaults[CourseSection :: PROPERTY_ID] = $course_section->get_id();
		$defaults[CourseSection :: PROPERTY_NAME] = $course_section->get_name();
		$defaults[CourseSection :: PROPERTY_VISIBLE] = is_null($course_section->get_visible())?1:$course_section->get_visible();
		parent :: setDefaults($defaults);
	}
	
	function get_course_section()
	{
		return $this->course_section;
	}
}
?>