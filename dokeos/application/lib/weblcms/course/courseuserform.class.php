<?php
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/courseusercategory.class.php';

class CourseUserForm extends FormValidator {
	
	const TYPE_EDIT = 2;
	
	private $course_code;
	
    function CourseUserForm($form_type, $course_code, $action) {
    	parent :: __construct('course_user', 'post', $action);
    	
    	$this->course_code = $course_code;
		$this->form_type = $form_type;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		
		$this->setDefaults();
    }
    
    function build_basic_form()
    {
    	$this->addElement('static', 'course', get_lang('CourseCode'));
    	
		$wdm = WeblcmsDataManager :: get_instance();
		$categories = $wdm->retrieve_course_user_categories();
		$cat_options['0'] = get_lang('NoCategory');
		
		while ($category = $categories->next_result())
		{
			$cat_options[$category->get_id()] = $category->get_title();
		}
		
		$this->addElement('select', 'user_course_cat', get_lang('Category'), $cat_options);
		
		$this->addElement('submit', 'course_user_category', get_lang('Ok'));
    }
    
    function build_editing_form()
    {
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', 'course_code');
    }
    
    function update_course_user_category()
    {
    	$values = $this->exportValues();
    	
		$wdm = WeblcmsDataManager :: get_instance();
    	return $wdm->update_course_rel_user_category(array($values['course_code'], $values['user_course_cat']));
    }
    
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$courseusercategory = $this->courseusercategory;
		$defaults['course'] = $this->course_code;
		$defaults['course_code'] = $this->course_code;
		parent :: setDefaults($defaults);
	}
}
?>