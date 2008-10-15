<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once Path :: get_admin_path(). 'settings/settings_admin_connector.class.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/../category_manager/course_category.class.php';


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
		$this->addElement('text', Course :: PROPERTY_VISUAL, Translation :: get('VisualCode'));
		$this->addRule(Course :: PROPERTY_VISUAL, Translation :: get('ThisFieldIsRequired'), 'required');

		if (!$this->user->is_platform_admin())
		{
			$this->addElement('text', Course :: PROPERTY_TITULAR, Translation :: get('Teacher'));
		}
		else
		{
			$udm = UserDataManager :: get_instance();
			$condition = new EqualityCondition(User :: PROPERTY_STATUS, 1);

			$user_options = array();
			$users = $udm->retrieve_users($condition);

			while ($user = $users->next_result())
			{
				$user_options[$user->get_id()] = $user->get_lastname() . '&nbsp;' . $user->get_firstname();
			}

			$this->addElement('select', Course :: PROPERTY_TITULAR, Translation :: get('Teacher'), $user_options);
		}
		$this->addRule(Course :: PROPERTY_TITULAR, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Course :: PROPERTY_NAME, Translation :: get('Title'));
		$this->addRule(Course :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$cat_options = array();
		$parent = $this->parent;
		
		$wdm = WeblcmsDataManager :: get_instance();
		$categories = $wdm->retrieve_course_categories();

		while ($category = $categories->next_result())
		{
			$cat_options[$category->get_id()] = $category->get_name();
		}

		$this->addElement('select', Course :: PROPERTY_CATEGORY, Translation :: get('Category'), $cat_options);

		$this->addElement('text', Course :: PROPERTY_EXTLINK_NAME, Translation :: get('Department'));
		$this->addElement('text', Course :: PROPERTY_EXTLINK_URL, Translation :: get('DepartmentUrl'));
		
		$adm = AdminDataManager :: get_instance();
		$lang_options = $adm->get_languages();
		$this->addElement('select', Course :: PROPERTY_LANGUAGE, Translation :: get('Language'), $lang_options);
		
		$course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', Weblcms :: APPLICATION_NAME);
		
		if ($course_can_have_theme)
		{
			$theme_options = array();
			$theme_options[''] = '-- ' . Translation :: get('PlatformDefault') . ' --';
			$theme_options = array_merge($theme_options, Theme :: get_themes());
			$this->addElement('select', Course :: PROPERTY_THEME, Translation :: get('Theme'), $theme_options);
		}

		$course_access = array();
		$course_access[] =& $this->createElement('radio', null, null, Translation :: get('CourseAccessOpenWorld'), COURSE_VISIBILITY_OPEN_WORLD);
		$course_access[] =& $this->createElement('radio', null, null, Translation :: get('CourseAccessOpenRegistered'), COURSE_VISIBILITY_OPEN_PLATFORM);
		$course_access[] =& $this->createElement('radio', null, null, Translation :: get('CourseAccessPrivate'), COURSE_VISIBILITY_REGISTERED);
		$course_access[] =& $this->createElement('radio', null, null, Translation :: get('CourseAccessClosed'), COURSE_VISIBILITY_CLOSED);
		$course_access[] =& $this->createElement('radio', null, null, Translation :: get('CourseAccessModified'), COURSE_VISIBILITY_MODIFIED);
		$this->addGroup($course_access, Course :: PROPERTY_VISIBILITY, Translation :: get('CourseAccess'), '<br />');

		$subscribe_allowed = array();
		$subscribe_allowed[] =& $this->createElement('radio', null, null, Translation :: get('SubscribeAllowed'), 1);
		$subscribe_allowed[] =& $this->createElement('radio', null, null, Translation :: get('SubscribeNotAllowed'), 0);
		$this->addGroup($subscribe_allowed, Course :: PROPERTY_SUBSCRIBE_ALLOWED, Translation :: get('Subscribe'), '<br />');

		$unsubscribe_allowed = array();
		$unsubscribe_allowed[] =& $this->createElement('radio', null, null, Translation :: get('UnsubscribeAllowed'), 1);
		$unsubscribe_allowed[] =& $this->createElement('radio', null, null, Translation :: get('UnsubscribeNotAllowed'), 0);
		$this->addGroup($unsubscribe_allowed, Course :: PROPERTY_UNSUBSCRIBE_ALLOWED, Translation :: get('Unsubscribe'), '<br />');

		$this->addElement('submit', 'course_settings', Translation :: get('Ok'));
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
    	$this->addElement('text', Course :: PROPERTY_ID, Translation :: get('CourseCode'));
    	$this->addRule(Course :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');
    	$this->build_basic_form();
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
		
		$course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', Weblcms :: APPLICATION_NAME);
		if ($course_can_have_theme)
		{
			$course->set_theme($values[Course :: PROPERTY_THEME]);
		}
		
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
    	$course->set_category($values[Course :: PROPERTY_CATEGORY]);
		$course->set_titular($values[Course :: PROPERTY_TITULAR]);
    	$course->set_extlink_name($values[Course :: PROPERTY_EXTLINK_NAME]);
    	$course->set_extlink_url($values[Course :: PROPERTY_EXTLINK_URL]);
    	$course->set_language($values[Course :: PROPERTY_LANGUAGE]);
    	
		$course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', Weblcms :: APPLICATION_NAME);
		if ($course_can_have_theme)
		{
			$course->set_theme($values[Course :: PROPERTY_THEME]);
		}
    	
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
				$user_id = $this->user->get_id();
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
		$defaults[Course :: PROPERTY_CATEGORY] = $course->get_category();
		$defaults[Course :: PROPERTY_EXTLINK_NAME] = $course->get_extlink_name();
		$defaults[Course :: PROPERTY_EXTLINK_URL] = $course->get_extlink_url();
		$defaults[Course :: PROPERTY_LANGUAGE] = $course->get_language();
		$defaults[Course :: PROPERTY_VISIBILITY] = $course->get_visibility();
		$defaults[Course :: PROPERTY_SUBSCRIBE_ALLOWED] = $course->get_subscribe_allowed();
		$defaults[Course :: PROPERTY_UNSUBSCRIBE_ALLOWED] = $course->get_unsubscribe_allowed();
		
		$course_can_have_theme = PlatformSetting :: get('allow_course_theme_selection', Weblcms :: APPLICATION_NAME);
		
		if ($course_can_have_theme)
		{
			$defaults[Course :: PROPERTY_THEME] = $course->get_theme();
		}
		
		parent :: setDefaults($defaults);
	}
}
?>