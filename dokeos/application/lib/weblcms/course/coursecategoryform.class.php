<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
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
		
		$coursecategory = $this->coursecategory;
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseCategory :: PROPERTY_AUTH_CAT_CHILD, true);
		$conditions[] = new NotCondition(new EqualityCondition(CourseCategory :: PROPERTY_CODE, $coursecategory->get_code()));
		$condition = new AndCondition($conditions);
		
		$wdm = WeblcmsDataManager :: get_instance();
		$categories = $wdm->retrieve_course_categories($condition);
		
		$cat_options['0'] = get_lang('NoCategory');
		while ($category = $categories->next_result())
		{
			$cat_options[$category->get_id()] = $category->get_name();
		}
		
		$this->addElement('select', CourseCategory :: PROPERTY_PARENT, get_lang('Parent'), $cat_options);
		
		$child_allowed = array();
		$child_allowed[] =& $this->createElement('radio', null, null, get_lang('Yes'), 1);
		$child_allowed[] =& $this->createElement('radio', null, null, get_lang('No'), 0);
		$this->addGroup($child_allowed, CourseCategory :: PROPERTY_AUTH_COURSE_CHILD, get_lang('CourseCategoryChildAllowed'), '<br />');
		
		$cat_allowed = array();
		$cat_allowed[] =& $this->createElement('radio', null, null, get_lang('Yes'), 1);
		$cat_allowed[] =& $this->createElement('radio', null, null, get_lang('No'), 0);
		$this->addGroup($cat_allowed, CourseCategory :: PROPERTY_AUTH_CAT_CHILD, get_lang('CourseCategoryCatAllowed'), '<br />');
				
		$this->addElement('submit', 'course_settings', get_lang('Ok'));
    }
    
    function build_editing_form()
    {
	   	$this->build_basic_form();
    	$this->addElement('hidden', CourseCategory :: PROPERTY_ID );
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
    	
    	$coursecategory->set_name($values[CourseCategory :: PROPERTY_NAME]);
    	$coursecategory->set_parent($values[CourseCategory :: PROPERTY_PARENT]);
    	$coursecategory->set_auth_course_child($values[CourseCategory :: PROPERTY_AUTH_COURSE_CHILD]);
    	$coursecategory->set_auth_cat_child($values[CourseCategory :: PROPERTY_AUTH_CAT_CHILD]);
    	
    	return $coursecategory->update();
    }
    
    function create_course_category()
    {
    	$coursecategory = $this->coursecategory;
    	$values = $this->exportValues();
    	
    	$coursecategory->set_name($values[CourseCategory :: PROPERTY_NAME]);
    	$coursecategory->set_code($values[CourseCategory :: PROPERTY_CODE]);
    	$coursecategory->set_parent($values[CourseCategory :: PROPERTY_PARENT]);
    	$coursecategory->set_auth_course_child($values[CourseCategory :: PROPERTY_AUTH_COURSE_CHILD]);
    	$coursecategory->set_auth_cat_child($values[CourseCategory :: PROPERTY_AUTH_CAT_CHILD]);
    	
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
		$defaults[CourseCategory :: PROPERTY_NAME] = $coursecategory->get_name();
		$defaults[CourseCategory :: PROPERTY_CODE] = $coursecategory->get_code();
		$defaults[CourseCategory :: PROPERTY_AUTH_COURSE_CHILD] = $coursecategory->get_auth_course_child();
		$defaults[CourseCategory :: PROPERTY_AUTH_CAT_CHILD] = $coursecategory->get_auth_cat_child();
		$defaults[CourseCategory :: PROPERTY_PARENT] = $coursecategory->get_parent();
		parent :: setDefaults($defaults);
	}
}
?>