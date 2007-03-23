<?php
require_once dirname(__FILE__).'/../../course/course.class.php';

class CourseSettingsForm extends FormValidator {
	
	private $parent;
	private $course;

    function CourseSettingsForm($parent) {
    	parent :: __construct('course_settings', 'post', $parent->get_url());
    	
    	$this->course = $parent->get_course();
    	$this->parent = $parent;
    	$this->build_editing_form();
    }
    
    function build_editing_form()
    {
    	$course = $this->course;
    	$parent = $this->parent;
    	
		$this->addElement('text', Course :: PROPERTY_VISUAL, get_lang('VisualCode'));
		$this->addRule(Course :: PROPERTY_VISUAL, get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', Course :: PROPERTY_TITULAR, get_lang('Teacher'));
		$this->addRule(Course :: PROPERTY_TITULAR, get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', Course :: PROPERTY_NAME, get_lang('Title'));
		$this->addRule(Course :: PROPERTY_NAME, get_lang('ThisFieldIsRequired'), 'required');
		
		$cat_options = array();
		$categories =  $parent->get_parent()->retrieve_course_categories();
		while ($category = $categories->next_result())
		{
			$cat_options[$category->get_code()] = $category->get_name();
		}
		
		$this->addElement('select', Course :: PROPERTY_CATEGORY, get_lang('Category'), $cat_options);
		 
		$this->addElement('text', Course :: PROPERTY_EXTLINK_NAME, get_lang('Department'));
		$this->addElement('text', Course :: PROPERTY_EXTLINK_URL, get_lang('DepartmentUrl'));
		
		/*
		 * TODO: SCARA - It's probably better to have a general language class somewhere ?
		 */
		
		$languages = api_get_languages();
		$lang_options = array();
		foreach ($languages['name'] as $index => $name)
		{
			$lang_options[$languages['folder'][$index]] = $name;
		}
		
		$this->addElement('select', Course :: PROPERTY_LANGUAGE, get_lang('Language'), $lang_options);
		 
		$course_access = array();
		$course_access[] =& $this->createElement('radio', null, null, get_lang('CourseAccessOpenWorld'), COURSE_VISIBILITY_OPEN_WORLD);
		$course_access[] =& $this->createElement('radio', null, null, get_lang('CourseAccessOpenRegistered'), COURSE_VISIBILITY_OPEN_PLATFORM);
		$course_access[] =& $this->createElement('radio', null, null, get_lang('CourseAccessPrivate'), COURSE_VISIBILITY_REGISTERED);
		$course_access[] =& $this->createElement('radio', null, null, get_lang('CourseAccessClosed'), COURSE_VISIBILITY_CLOSED);
		$course_access[] =& $this->createElement('radio', null, null, get_lang('CourseAccessModified'), COURSE_VISIBILITY_MODIFIED);
		$this->addGroup($course_access, Course :: PROPERTY_VISIBILITY, get_lang('CourseAccess'), '<br />');
		
		$subscribe_allowed = array();
		$subscribe_allowed[] =& $this->createElement('radio', null, null, get_lang('SubscribeAllowed'), 1);
		$subscribe_allowed[] =& $this->createElement('radio', null, null, get_lang('SubscribeNotAllowed'), 0);
		$this->addGroup($subscribe_allowed, Course :: PROPERTY_SUBSCRIBE_ALLOWED, get_lang('Subscribe'), '<br />');
		
		$unsubscribe_allowed = array();
		$unsubscribe_allowed[] =& $this->createElement('radio', null, null, get_lang('UnsubscribeAllowed'), 1);
		$unsubscribe_allowed[] =& $this->createElement('radio', null, null, get_lang('UnsubscribeNotAllowed'), 0);
		$this->addGroup($unsubscribe_allowed, Course :: PROPERTY_UNSUBSCRIBE_ALLOWED, get_lang('Unsubscribe'), '<br />');		
		
		$this->addElement('submit', 'course_settings', get_lang('Ok'));
    	$defaults = array();
		$defaults[Course :: PROPERTY_VISUAL] = $course->get_visual();
		$defaults[Course :: PROPERTY_TITULAR] = $course->get_titular();
		$defaults[Course :: PROPERTY_NAME] = $course->get_name();
		$defaults[Course :: PROPERTY_CATEGORY] = $course->get_category()->get_code();
		$defaults[Course :: PROPERTY_EXTLINK_NAME] = $course->get_extlink_name();
		$defaults[Course :: PROPERTY_EXTLINK_URL] = $course->get_extlink_url();
		$defaults[Course :: PROPERTY_LANGUAGE] = $course->get_language();
		$defaults[Course :: PROPERTY_VISIBILITY] = $course->get_visibility();
		$defaults[Course :: PROPERTY_SUBSCRIBE_ALLOWED] = $course->get_subscribe_allowed();
		$defaults[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED] = $course->get_unsubscribe_allowed();
    	$this->setDefaults($defaults);
    }
    
    function update_course()
    {
    	$course = $this->course;
    	$values = $this->exportValues();
    	
    	$course->set_visual($values[Course :: PROPERTY_VISUAL]);
    	$course->set_name($values[Course :: PROPERTY_NAME]);
    	$course->set_category($values[Course :: PROPERTY_CATEGORY]);
    	$course->set_titular($values[Course :: PROPERTY_TITULAR]);
    	$course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
    	$course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
    	$course->set_language($values[Course :: PROPERTY_LANGUAGE]);
    	$course->set_visibility($values[Course :: PROPERTY_VISIBILITY]);
    	$course->set_subscribe_allowed($values[Course :: PROPERTY_SUBSCRIBE_ALLOWED]);
    	$course->set_unsubscribe_allowed($values[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED]);
    	
    	return $course->update();
    }
}
?>