<?php
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/coursecategory.class.php';

class CourseCategoryForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';
	
	private $coursecategory;

    function CourseCategoryForm($form_type, $coursecategory, $action) {
    	parent :: __construct('course_category', 'post', $action);
    	
    	$this->coursecategory = $coursecategory;
    	
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
		$this->addElement('text', CourseCategory :: PROPERTY_NAME, get_lang('CourseCategoryName'));
		$this->addRule(CourseCategory :: PROPERTY_NAME, get_lang('ThisFieldIsRequired'), 'required');
		
		$cat_options = array();
		
		$condition = new EqualityCondition(CourseCategory :: PROPERTY_AUTH_CAT_CHILD, true);
		$wdm = WeblcmsDataManager :: get_instance();
		$categories = $wdm->retrieve_course_categories($condition);
		
		while ($category = $categories->next_result())
		{
			$cat_options[$category->get_code()] = $category->get_name();
		}
		
		$this->addElement('select', CourseCategory :: PROPERTY_PARENT, get_lang('Parent'), $cat_options);
		
		$child_allowed = array();
		$child_allowed[] =& $this->createElement('radio', null, null, get_lang('Yes'), true);
		$child_allowed[] =& $this->createElement('radio', null, null, get_lang('No'), false);
		$this->addGroup($child_allowed, CourseCategory :: PROPERTY_AUTH_COURSE_CHILD, get_lang('CourseCategoryChildAllowed'), '<br />');
		
		$cat_allowed = array();
		$cat_allowed[] =& $this->createElement('radio', null, null, get_lang('Yes'), true);
		$cat_allowed[] =& $this->createElement('radio', null, null, get_lang('No'), false);
		$this->addGroup($cat_allowed, CourseCategory :: PROPERTY_AUTH_CAT_CHILD, get_lang('CourseCategoryCatAllowed'), '<br />');
				
		$this->addElement('submit', 'course_settings', get_lang('Ok'));
    }
    
    function build_editing_form()
    {
    	$course = $this->course;
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', CourseCategory :: PROPERTY_ID);
    }
    
    function build_creation_form()
    {
		$this->addElement('text', CourseCategory :: PROPERTY_CODE, get_lang('CourseCategoryCode'));
		$this->addRule(CourseCategory :: PROPERTY_CODE, get_lang('ThisFieldIsRequired'), 'required');
    	$this->build_basic_form();
    }
    
    function update_course_category()
    {
    	$coursecategory = $this->coursecategory;
    	$values = $this->exportValues();
    	
    	$coursecategory->set_visual($values[CourseCategory :: PROPERTY_VISUAL]);
    	$coursecategory->set_name($values[CourseCategory :: PROPERTY_NAME]);
    	$coursecategory->set_category_code($values[CourseCategory :: PROPERTY_CATEGORY_CODE]);
    	$coursecategory->set_titular($values[CourseCategory :: PROPERTY_TITULAR]);
    	$coursecategory->set_extlink_name($values[CourseCategory :: PROPERTY_EXTLINK_NAME]);
    	$coursecategory->set_extlink_url($values[CourseCategory :: PROPERTY_EXTLINK_URL]);
    	$coursecategory->set_language($values[CourseCategory :: PROPERTY_LANGUAGE]);
    	$coursecategory->set_visibility($values[CourseCategory :: PROPERTY_VISIBILITY]);
    	$coursecategory->set_subscribe_allowed($values[CourseCategory :: PROPERTY_SUBSCRIBE_ALLOWED]);
    	$coursecategory->set_unsubscribe_allowed($values[CourseCategory :: PROPERTY_UNSUBSCRIBE_ALLOWED]);
    	
    	return $coursecategory->update();
    }
    
    function create_course_category()
    {
    	$coursecategory = $this->coursecategory;
    	$values = $this->exportValues();
    	
    	$coursecategory->set_id($values[CourseCategory :: PROPERTY_ID]);
    	$coursecategory->set_visual($values[CourseCategory :: PROPERTY_VISUAL]);
    	$coursecategory->set_name($values[CourseCategory :: PROPERTY_NAME]);
    	$coursecategory->set_category_code($values[CourseCategory :: PROPERTY_CATEGORY_CODE]);
    	$coursecategory->set_titular($values[CourseCategory :: PROPERTY_TITULAR]);
    	$coursecategory->set_extlink_name($values[CourseCategory :: PROPERTY_EXTLINK_NAME]);
    	$coursecategory->set_extlink_url($values[CourseCategory :: PROPERTY_EXTLINK_URL]);
    	$coursecategory->set_language($values[CourseCategory :: PROPERTY_LANGUAGE]);
    	$coursecategory->set_visibility($values[CourseCategory :: PROPERTY_VISIBILITY]);
    	$coursecategory->set_subscribe_allowed($values[CourseCategory :: PROPERTY_SUBSCRIBE_ALLOWED]);
    	$coursecategory->set_unsubscribe_allowed($values[CourseCategory :: PROPERTY_UNSUBSCRIBE_ALLOWED]);
    	
    	return $coursecategory->create();
    }
    
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$coursecategory = $this->coursecategory;
		$defaults[CourseCategory :: PROPERTY_ID] = $coursecategory->get_id();
		$defaults[CourseCategory :: PROPERTY_NAME] = $coursecategory->get_name();
		$defaults[CourseCategory :: PROPERTY_CODE] = $coursecategory->get_code();
		$defaults[CourseCategory :: PROPERTY_AUTH_COURSE_CHILD] = $coursecategory->get_auth_course_child();
		$defaults[CourseCategory :: PROPERTY_AUTH_CAT_CHILD] = $coursecategory->get_auth_cat_child();
		$defaults[CourseCategory :: PROPERTY_PARENT] = $coursecategory->get_parent();
		parent :: setDefaults($defaults);
	}
}
?>