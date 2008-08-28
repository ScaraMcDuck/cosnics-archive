<?php
/**
 * @package application.lib.profiler
 */
require_once dirname(__FILE__) . '/system_announcement.class.php';
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once Path :: get_plugin_path() . 'html2text/class.html2text.inc';
require_once Path :: get_user_path() . 'lib/users_data_manager.class.php';
require_once Path :: get_class_group_path() . 'lib/class_group_data_manager.class.php';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class SystemAnnouncementForm extends FormValidator
{
   /**#@+
    * Constant defining a form parameter
 	*/
 	
	const PARAM_FOREVER = 'forever';
	const PARAM_FROM_DATE = 'from_date';
	const PARAM_TO_DATE = 'to_date';
	const PARAM_TARGETS = 'target_users_and_groups';
	const PARAM_RECEIVERS = 'receivers';
	const PARAM_TARGETS_TO = 'to';
	const PARAM_TARGET_USER_PREFIX = 'user';
	const PARAM_TARGET_GROUP_PREFIX = 'group';
	
	const STATUS_NORMAL = 1;
	const STATUS_ERROR = 2;
	const STATUS_WARNING = 3;
	const STATUS_INFO = 4;

	/**#@-*/
	/**
	 * The learning object that will be published
	 */
	private $learning_object;
	/**
	 * The publication that will be changed (when using this form to edit a
	 * publication)
	 */
	private $form_user;
	
	private $system_announcement;

	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function SystemAnnouncementForm($learning_object, $form_user, $action)
    {
		parent :: __construct('publish', 'post', $action);
		$this->learning_object = $learning_object;
		$this->form_user = $form_user;
		$this->build_form();
		$this->setDefaults();
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
    	$status_options = array();
    	$status_options[self :: STATUS_NORMAL] = Translation :: get('Confirmation');
    	$status_options[self :: STATUS_INFO] = Translation :: get('Information');
    	$status_options[self :: STATUS_ERROR] = Translation :: get('Error');
    	$status_options[self :: STATUS_WARNING] = Translation :: get('Warning');
    	
		$this->addElement('select', SystemAnnouncement :: PROPERTY_STATUS, Translation :: get('Status'), $status_options);

		$receiver_options = $this->get_receiver_options();
		$attributes = array(self :: PARAM_RECEIVERS => $receiver_options);
		$this->addElement('receivers', self :: PARAM_TARGETS, Translation :: get('PublishFor'), $attributes);

		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', SystemAnnouncement :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
		//$this->addElement('checkbox', SystemAnnouncement :: PROPERTY_EMAIL_SENT, Translation :: get('SendByEMail'));
		$this->addElement('submit', 'submit', Translation :: get('Ok'));
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
			$from = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
			$to = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
		}
		$hidden = ($values[SystemAnnouncement :: PROPERTY_HIDDEN] ? 1 : 0);
		
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

		$pub = new SystemAnnouncement();
		$pub->set_learning_object_id($this->learning_object->get_id());
		$pub->set_publisher($this->form_user->get_id());
		$pub->set_published(time());
		$pub->set_modified(time());
		$pub->set_hidden($hidden);
		$pub->set_from_date($from);
		$pub->set_to_date($to);
		$pub->set_status($values[SystemAnnouncement :: PROPERTY_STATUS]);

		if ($pub->create())
		{
			return true;
		}
		else
		{
			return false;
		}
    }
    
    function get_receiver_options()
    {
    	$receiver_options = array();
    	
    	$class_groups = ClassGroupDataManager :: get_instance()->retrieve_classgroups();
    	
    	while ($class_group = $class_groups->next_result())
    	{
    		$receiver_choices[self :: PARAM_TARGET_GROUP_PREFIX.'-'.$class_group->get_id()] = Translation :: get('Group').': '.$class_group->get_name();
    	}
    	
    	$users = UsersDataManager :: get_instance()->retrieve_users();
    	
    	while ($user = $users->next_result())
    	{
    		$receiver_options[self :: PARAM_TARGET_USER_PREFIX.'-'.$user->get_id()] = $user->get_fullname();
    	}
    	
    	return $receiver_options;
    }
    
    function set_system_announcement($system_announcement)
    {
    	$this->system_announcement = $system_announcement;
		$this->addElement('hidden','said');
		$this->addElement('hidden','action');
		$defaults['action'] = 'edit';
		$defaults['said'] = $system_announcement->get_id();
		$defaults[SystemAnnouncement :: PROPERTY_FROM_DATE] = $system_announcement->get_from_date();
		$defaults[SystemAnnouncement :: PROPERTY_TO_DATE] = $system_announcement->get_to_date();
		$defaults[SystemAnnouncement :: PROPERTY_STATUS] = $system_announcement->get_status();
		if($defaults[SystemAnnouncement :: PROPERTY_FROM_DATE] != 0)
		{
			$defaults[self :: PARAM_FOREVER] = 0;
		}
		$defaults[SystemAnnouncement :: PROPERTY_HIDDEN] = $system_announcement->is_hidden();
		$users = $system_announcement->get_target_users();
		foreach($users as $user)
		{
			$defaults[self :: PARAM_TARGETS][self :: PARAM_TARGETS_TO][] = self :: PARAM_TARGET_USER_PREFIX . '-'.$user;
		}
		$class_groups = $system_announcement->get_target_class_groups();
		foreach($class_groups as $index => $class_group)
		{
			$defaults[self :: PARAM_TARGETS][self :: PARAM_TARGETS_TO][] = self :: PARAM_TARGET_GROUP_PREFIX . '-'.$class_group;
		}
		
		if (count($users) > 0 || count($class_groups) > 0)
		{
			$defaults[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] = '1';
		}
		
		parent::setDefaults($defaults);
    }
}
?>