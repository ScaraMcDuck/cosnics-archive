<?php
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/courseuserrelation.class.php';

class CourseUserRelationForm extends FormValidator {
	
	const TYPE_EDIT = 2;
	
	private $courseuserrelation;
	
    function CourseUserRelationForm($form_type, $courseuserrelation, $action) {
    	parent :: __construct('course_user', 'post', $action);
    	
    	$this->courseuserrelation = $courseuserrelation;
		$this->form_type = $form_type;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		
		$this->setDefaults();
    }
    
    function build_basic_form()
    {
    	$this->addElement('static', Course :: PROPERTY_ID, get_lang('CourseCode'));
    	
		$wdm = WeblcmsDataManager :: get_instance();
		$categories = $wdm->retrieve_course_user_categories();
		$cat_options['0'] = get_lang('NoCategory');
		
		while ($category = $categories->next_result())
		{
			$cat_options[$category->get_id()] = $category->get_title();
		}
		
		$this->addElement('select', CourseUserRelation :: PROPERTY_CATEGORY, get_lang('Category'), $cat_options);
		
		$this->addElement('submit', 'course_user_category', get_lang('Ok'));
    }
    
    function build_editing_form()
    {
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', CourseUserRelation :: PROPERTY_COURSE);
    }
    
    function update_course_user_relation()
    {
    	$courseuserrelation = $this->courseuserrelation;
    	$values = $this->exportValues();
    	
		$conditions = array();
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, api_get_user_id());
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $values[CourseUserRelation :: PROPERTY_CATEGORY]);		
		$condition = new AndCondition($conditions);
		
		$wdm = WeblcmsDataManager :: get_instance();
		$sort = $wdm->retrieve_max_sort_value('course_rel_user', CourseUserRelation :: PROPERTY_SORT, $condition);
    	
    	$courseuserrelation->set_category($values[CourseUserRelation :: PROPERTY_CATEGORY]);
    	$courseuserrelation->set_sort($sort+1);
    	
    	return $courseuserrelation->update();
    }
    
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$courseuserrelation = $this->courseuserrelation;
		$defaults[Course :: PROPERTY_ID] = $courseuserrelation->get_course();
		$defaults[CourseUserRelation :: PROPERTY_COURSE] = $courseuserrelation->get_course();
		$defaults[CourseUserRelation :: PROPERTY_CATEGORY] = $courseuserrelation->get_category();
		parent :: setDefaults($defaults);
	}
}
?>