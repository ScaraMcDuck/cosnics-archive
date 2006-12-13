<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/learningobjectpublication.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/html2text.class.php';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class LearningObjectPublicationForm extends FormValidator
{
   /**#@+
    * Constant defining a form parameter
 	*/
	// XXX: Some of these constants heavily depend on FormValidator.
	const PARAM_CATEGORY_ID = 'category';
	const PARAM_TARGETS = 'target_users_and_groups';
	const PARAM_RECEIVERS = 'receivers';
	const PARAM_TARGETS_TO = 'to';
	const PARAM_TARGET_USER_PREFIX = 'user';
	const PARAM_TARGET_GROUP_PREFIX = 'group';
	const PARAM_FOREVER = 'forever';
	const PARAM_FROM_DATE = 'from_date';
	const PARAM_TO_DATE = 'to_date';
	const PARAM_HIDDEN = 'hidden';
	const PARAM_EMAIL = 'email';
	/**#@-*/
	/**
	 * The tool in which the publication will be made
	 */
	private $tool;
	/**
	 * The learning object that will be published
	 */
	private $learning_object;
	/**
	 * The publication that will be changed (when using this form to edit a
	 * publication)
	 */
	private $publication;
	/**
	 * Is a 'send by email' option available?
	 */
	private $email_option;
	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function LearningObjectPublicationForm($learning_object, $tool, $email_option = false)
    {
    	$url = $tool->get_url(array (LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
		parent :: __construct('publish', 'post', $url);
		$this->tool = $tool;
		$this->learning_object = $learning_object;
		$this->email_option = $email_option;
		$this->build_form();
		$this->setDefaults();
    }
    /**
     * Sets the publication. Use this function if you're using this form to
     * change the settings of a learning object publication.
     * @param LearningObjectPublication $publication
     */
    function set_publication($publication)
    {
    	$this->publication = $publication;
		$this->addElement('hidden','pid');
		$this->addElement('hidden','action');
		$defaults['action'] = 'edit';
		$defaults['pid'] = $publication->get_id();
		$defaults['from_date'] = $publication->get_from_date();
		$defaults['to_date'] = $publication->get_to_date();
		if($defaults['from_date'] != 0)
		{
			$defaults['forever'] = 0;
		}
		$defaults['hidden'] = $publication->is_hidden();
		$users = $publication->get_target_users();
		foreach($users as $index => $user_id)
		{
			$defaults['target_users_and_groups']['to'][] = 'user-'.$user_id;
		}
		$groups = $publication->get_target_groups();
		foreach($groups as $index => $group_id)
		{
			$defaults['target_users_and_groups']['to'][] = 'group-'.$group_id;
		}
		parent::setDefaults($defaults);
    }
	/**
	 * Sets the default values of the form.
	 *
	 * By default the publication is for everybody who has access to the tool
	 * and the publication will be available forever.
	 */
    function setDefaults()
    {
    	$defaults = array();
    	$defaults[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] = 0;
		$defaults[self :: PARAM_FOREVER] = 1;
		parent :: setDefaults($defaults);
    }
	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
		$categories = $this->tool->get_categories(true);
		if(count($categories) > 1)
		{
			// More than one category -> let user select one
			$this->addElement('select', self :: PARAM_CATEGORY_ID, get_lang('Category'), $categories);
		}
		else
		{
			// Only root category -> store object in root category
			$this->addElement('hidden',LearningObjectPublication :: PROPERTY_CATEGORY_ID,0);
		}
		$users = CourseManager::get_user_list_from_course_code(api_get_course_id());
		$receiver_choices = array();
		foreach($users as $index => $user)
		{
			$receiver_choices[self :: PARAM_TARGET_USER_PREFIX.'-'.$user['user_id']] = $user['firstName'].' '.$user['lastName'];
		}
		// TODO: Next lines reconnect to dokeos-database due
		// to conflict with DB-connection in repository. This problem
		// should be fixed.
		global $dbHost,$dbLogin,$dbPass,$mainDbName;
		mysql_connect($dbHost,$dbLogin,$dbPass);
		mysql_select_db($mainDbName);
		// --
		$groups = GroupManager::get_group_list();
		foreach($groups as $index => $group)
		{
			$receiver_choices[self :: PARAM_TARGET_GROUP_PREFIX.'-'.$group['id']] = $group['name'];
		}
		$attributes = array(self :: PARAM_RECEIVERS => $receiver_choices);
		$this->addElement('receivers', self :: PARAM_TARGETS, get_lang('PublishFor'),$attributes);
		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', self :: PARAM_HIDDEN, get_lang('Hidden'));
		if($this->email_option)
		{
			$this->addElement('checkbox', self::PARAM_EMAIL, get_lang('SendByEmail'));
		}
		$this->addElement('submit', 'submit', get_lang('Ok'));
    }
    /**
     * Updates a learning object publication using the values from the form.
     * @return LearningObjectPublication The updated publication
     * @todo This function shares some code with function
     * create_learning_object_publication. This code duplication should be
     * resolved.
     */
    function update_learning_object_publication()
    {
		$values = $this->exportValues();
		if ($values[self :: PARAM_FOREVER] != 0)
		{
			$from = $to = 0;
		}
		else
		{
			$from = RepositoryUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
			$to = RepositoryUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
		}
		$hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
		$category = $values[self :: PARAM_CATEGORY_ID];
		$users = array ();
		$groups = array ();
		if($values[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] == 1)
		{
			foreach($values[self::PARAM_TARGETS][self :: PARAM_TARGETS_TO] as $index => $target)
			{
				list($type,$id) = explode('-',$target);
				if($type == self :: PARAM_TARGET_GROUP_PREFIX)
				{
					$groups[] = $id;
				}
				elseif($type == self :: PARAM_TARGET_USER_PREFIX)
				{
					$users[] = $id;
				}
			}
		}
		$pub = $this->publication;
		$pub->set_from_date($from);
		$pub->set_to_date($to);
		$pub->set_hidden($hidden);
		$pub->set_target_users($users);
		$pub->set_target_groups($groups);
		$pub->update();
		return $pub;
    }
	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_learning_object_publication()
    {
		$values = $this->exportValues();
		if ($values[self :: PARAM_FOREVER] != 0)
		{
			$from = $to = 0;
		}
		else
		{
			$from = RepositoryUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
			$to = RepositoryUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
		}
		$hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
		$category = $values[self :: PARAM_CATEGORY_ID];
		$users = array ();
		$groups = array ();
		if($values[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] == 1)
		{
			foreach($values[self::PARAM_TARGETS][self :: PARAM_TARGETS_TO] as $index => $target)
			{
				list($type,$id) = explode('-',$target);
				if($type == self :: PARAM_TARGET_GROUP_PREFIX)
				{
					$groups[] = $id;
				}
				elseif($type == self :: PARAM_TARGET_USER_PREFIX)
				{
					$users[] = $id;
				}
			}
		}
		$course = $this->tool->get_course_id();
		$tool = $this->tool->get_tool_id();
		$dm = WeblcmsDataManager :: get_instance();
		$displayOrder = $dm->get_next_learning_object_publication_display_order_index($course,$tool,$category);
		$publisher = $this->tool->get_user_id();
		$publicationDate = time();
		$pub = new LearningObjectPublication(null, $this->learning_object, $course, $tool, $category, $users, $groups, $from, $to, $publisher, $publicationDate, $hidden, $displayOrder, false);
		$pub->create();
		if($this->email_option && $values[self::PARAM_EMAIL])
		{
			$learning_object = $this->learning_object;
			$display = LearningObjectDisplay::factory($learning_object);
			$subject = '['.api_get_setting('siteName').'] '.$learning_object->get_title();
			$body = new html2text($display->get_full_html());
			//@todo: send email to correct users/groups. For testing, the email is sent now to the publisher.
			$user = api_get_user_info();
			if(api_send_mail($user['mail'],$learning_object->get_title(),$body->get_text()))
			{
				$pub->set_email_sent(true);
			}
			$pub->update();
		}
		return $pub;
    }
}
?>