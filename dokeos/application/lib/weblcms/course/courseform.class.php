<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../../main/inc/lib/add_course.lib.inc.php';
require_once dirname(__FILE__).'/../../../../users/lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/user.class.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/coursecategory.class.php';

class CourseForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';

	private $parent;
	private $course;
	private $user;
	private $form_type;

    function CourseForm($form_type, $course, $user, $action) {
    	parent :: __construct('course_settings', 'post', $action);

    	$this->course = $course;
    	$this->user = $user;

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
		$this->addElement('text', Course :: PROPERTY_VISUAL, get_lang('VisualCode'));
		$this->addRule(Course :: PROPERTY_VISUAL, get_lang('ThisFieldIsRequired'), 'required');

		if (!$this->user->is_platform_admin())
		{
			$this->addElement('text', Course :: PROPERTY_TITULAR, get_lang('Teacher'));
		}
		else
		{
			$udm = UsersDataManager :: get_instance();
			$condition = new EqualityCondition(User :: PROPERTY_STATUS, 1);

			$user_options = array();
			$users = $udm->retrieve_users($condition);

			while ($user = $users->next_result())
			{
				$user_options[$user->get_user_id()] = $user->get_lastname() . '&nbsp;' . $user->get_firstname();
			}

			$this->addElement('select', Course :: PROPERTY_TITULAR, get_lang('Teacher'), $user_options);
		}
		$this->addRule(Course :: PROPERTY_TITULAR, get_lang('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Course :: PROPERTY_NAME, get_lang('Title'));
		$this->addRule(Course :: PROPERTY_NAME, get_lang('ThisFieldIsRequired'), 'required');

		$cat_options = array();
		$parent = $this->parent;

		$condition = new EqualityCondition(CourseCategory :: PROPERTY_AUTH_COURSE_CHILD, true);
		$wdm = WeblcmsDataManager :: get_instance();
		$categories = $wdm->retrieve_course_categories($condition);

		while ($category = $categories->next_result())
		{
			$cat_options[$category->get_code()] = $category->get_name();
		}

		$this->addElement('select', Course :: PROPERTY_CATEGORY_CODE, get_lang('Category'), $cat_options);

		$this->addElement('text', Course :: PROPERTY_EXTLINK_NAME, get_lang('Department'));
		$this->addElement('text', Course :: PROPERTY_EXTLINK_URL, get_lang('DepartmentUrl'));

		/*
		 * TODO: SCARA - It's probably better to have a general language class somewhere ?
		 */
		 
		$adm = AdminDataManager :: get_instance();
		$languages = $adm->retrieve_languages();
		$lang_options = array();
		
		while ($language = $languages->next_result())
		{
			$lang_options[$language->get_folder()] = $language->get_english_name();	
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
    }

    function build_editing_form()
    {
    	$course = $this->course;
    	$parent = $this->parent;

    	$this->build_basic_form();

    	$this->addElement('hidden', Course :: PROPERTY_ID);
    }

    function build_creation_form()
    {
    	$this->addElement('text', Course :: PROPERTY_ID, get_lang('CourseCode'));
    	$this->addRule(Course :: PROPERTY_ID, get_lang('ThisFieldIsRequired'), 'required');
    	$this->build_basic_form();
    }

    function update_course()
    {
    	$course = $this->course;
    	$values = $this->exportValues();

    	$course->set_visual($values[Course :: PROPERTY_VISUAL]);
    	$course->set_name($values[Course :: PROPERTY_NAME]);
    	$course->set_category_code($values[Course :: PROPERTY_CATEGORY_CODE]);
    	$course->set_titular($values[Course :: PROPERTY_TITULAR]);
    	$course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
    	$course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
    	$course->set_language($values[Course :: PROPERTY_LANGUAGE]);
    	$course->set_visibility($values[Course :: PROPERTY_VISIBILITY]);
    	$course->set_subscribe_allowed($values[Course :: PROPERTY_SUBSCRIBE_ALLOWED]);
    	$course->set_unsubscribe_allowed($values[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED]);

    	return $course->update();
    }

    function create_course()
    {
    	$course = $this->course;
    	$values = $this->exportValues();

    	$course->set_id($values[Course :: PROPERTY_ID]);
    	$course->set_visual($values[Course :: PROPERTY_VISUAL]);
    	$course->set_name($values[Course :: PROPERTY_NAME]);
    	$course->set_category_code($values[Course :: PROPERTY_CATEGORY_CODE]);

		if (!$this->user->is_platform_admin())
		{
			$titular = $values[Course :: PROPERTY_TITULAR];
		}
		else
		{
			$udm = UsersDataManager :: get_instance();
			$user = $udm->retrieve_user($values[Course :: PROPERTY_TITULAR]);
			$titular = $user->get_lastname(). ' ' .$user->get_firstname();
		}

		$course->set_titular($titular);
    	$course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
    	$course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
    	$course->set_language($values[Course :: PROPERTY_LANGUAGE]);
    	$course->set_visibility($values[Course :: PROPERTY_VISIBILITY]);
    	$course->set_subscribe_allowed($values[Course :: PROPERTY_SUBSCRIBE_ALLOWED]);
    	$course->set_unsubscribe_allowed($values[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED]);

    	if ($course->create())
    	{
    		// TODO: Temporary function pending revamped roles&rights system
    		//add_course_role_right_location_values($course->get_id());

    		$wdm = WeblcmsDataManager :: get_instance();
			if (!$this->user->is_platform_admin())
			{
				$user_id = $this->user->get_user_id();
			}
			else
			{
				$user_id = $values[Course :: PROPERTY_TITULAR];
			}

    		if ($wdm->subscribe_user_to_course($course, '1', '1', $user_id))
   			{
   				return true;
   			}
   			else
   			{
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }

	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$course = $this->course;
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
		parent :: setDefaults($defaults);
	}
}
?>