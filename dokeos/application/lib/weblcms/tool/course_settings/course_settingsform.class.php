<?php
require_once dirname(__FILE__).'/../../course.class.php';

class CourseSettingsForm extends FormValidator {

    function CourseSettingsForm($parent) {
    	parent :: __construct('course_settings', 'post', $parent->get_url());
    	
    	$course = $parent->get_course();
    	
		$this->addElement('text', Course :: PROPERTY_VISUAL, get_lang('VisualCode'));
		$this->addRule(Course :: PROPERTY_VISUAL, get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', Course :: PROPERTY_TITULAR, get_lang('Teacher'));
		$this->addRule(Course :: PROPERTY_TITULAR, get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', Course :: PROPERTY_NAME, get_lang('Title'));
		$this->addRule(Course :: PROPERTY_NAME, get_lang('ThisFieldIsRequired'), 'required');
		
		/*
		 * TODO: SCARA - Add course category dropdown here, requires implementation of course category objects !
		 */
		$this->addElement('static', Course :: PROPERTY_CATEGORY_CODE, get_lang('Category'));
		 
		$this->addElement('text', Course :: PROPERTY_EXTLINK_NAME, get_lang('Department'));
		$this->addElement('text', Course :: PROPERTY_EXTLINK_URL, get_lang('DepartmentUrl'));
		
		/*
		 * TODO: SCARA - Add course language dropdown here
		 */
		$this->addElement('static', Course :: PROPERTY_LANGUAGE, get_lang('Language'));
		 
		$course_access = array();
		$course_access[] =& $this->createElement('radio', Course :: PROPERTY_VISIBILITY, null, get_lang('CourseAccessOpenWorld'), COURSE_VISIBILITY_OPEN_WORLD);
		$course_access[] =& $this->createElement('radio', Course :: PROPERTY_VISIBILITY, null, get_lang('CourseAccessOpenRegistered'), COURSE_VISIBILITY_OPEN_PLATFORM);
		$course_access[] =& $this->createElement('radio', Course :: PROPERTY_VISIBILITY, null, get_lang('CourseAccessPrivate'), COURSE_VISIBILITY_REGISTERED);
		$course_access[] =& $this->createElement('radio', Course :: PROPERTY_VISIBILITY, null, get_lang('CourseAccessClosed'), COURSE_VISIBILITY_CLOSED);
		$course_access[] =& $this->createElement('radio', Course :: PROPERTY_VISIBILITY, null, get_lang('CourseAccessModified'), COURSE_VISIBILITY_MODIFIED);
		$this->addGroup($course_access, Course :: PROPERTY_VISIBILITY, get_lang('CourseAccess'), '<br />');
		
		$subscribe_allowed = array();
		$subscribe_allowed[] =& $this->createElement('radio', Course :: PROPERTY_SUBSCRIBE_ALLOWED, null, get_lang('SubscribeAllowed'), 1);
		$subscribe_allowed[] =& $this->createElement('radio', Course :: PROPERTY_SUBSCRIBE_ALLOWED, null, get_lang('SubscribeNotAllowed'), 0);
		$this->addGroup($subscribe_allowed, Course :: PROPERTY_SUBSCRIBE_ALLOWED, get_lang('Subscription'), '<br />');
		
		$unsubscribe_allowed = array();
		$unsubscribe_allowed[] =& $this->createElement('radio', Course :: PROPERTY_UNSUBSCRIBE_ALLOWED, null, get_lang('UnsubscribeAllowed'), 1);
		$unsubscribe_allowed[] =& $this->createElement('radio', Course :: PROPERTY_UNSUBSCRIBE_ALLOWED, null, get_lang('UnsubscribeNotAllowed'), 0);
		$this->addGroup($unsubscribe_allowed, Course :: PROPERTY_UNSUBSCRIBE_ALLOWED, get_lang('Unsubscribe'), '<br />');		
		
		$this->addElement('submit', 'course_settings', get_lang('Ok'));
    	$defaults = array();
		$defaults[Course :: PROPERTY_VISUAL] = $course->get_visual();
		$defaults[Course :: PROPERTY_TITULAR] = $course->get_titular();
		$defaults[Course :: PROPERTY_NAME] = $course->get_name();
		$defaults[Course :: PROPERTY_CATEGORY_CODE] = $course->get_category_code();
		$defaults[Course :: PROPERTY_EXTLINK_NAME] = $course->get_extlink_name();
		$defaults[Course :: PROPERTY_EXTLINK_URL] = $course->get_extlink_url();
		$defaults[Course :: PROPERTY_LANGUAGE] = $course->get_language();
		$defaults[Course :: PROPERTY_VISIBILITY] = $course->get_visibility();
		$defaults[Course :: PROPERTY_SUBSCRIBE_ALLOWED] = $course->get_subscribe_allowed();
		$defaults[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED] = $course->get_unsubscribe_allowed();
    	$this->setDefaults($defaults);
    }
}
?>