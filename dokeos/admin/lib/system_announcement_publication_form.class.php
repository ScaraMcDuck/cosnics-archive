<?php
/**
 * @package application.lib.profiler
 */
require_once dirname(__FILE__) . '/system_announcement_publication.class.php';
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once Path :: get_plugin_path() . 'html2text/class.html2text.inc';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
require_once Path :: get_group_path() . 'lib/group_data_manager.class.php';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class SystemAnnouncementPublicationForm extends FormValidator
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
	
	private $system_announcement_publication;

	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function SystemAnnouncementPublicationForm($learning_object, $form_user, $action)
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
		$receiver_options = $this->get_receiver_options();
		$attributes = array(self :: PARAM_RECEIVERS => $receiver_options);
		$this->addElement('receivers', self :: PARAM_TARGETS, Translation :: get('PublishFor'), $attributes);

		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', SystemAnnouncementPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
		//$this->addElement('checkbox', SystemAnnouncementPublication :: PROPERTY_EMAIL_SENT, Translation :: get('SendByEMail'));
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
		$hidden = ($values[SystemAnnouncementPublication :: PROPERTY_HIDDEN] ? 1 : 0);
		
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

		$pub = new SystemAnnouncementPublication();
		$pub->set_learning_object_id($this->learning_object->get_id());
		$pub->set_publisher($this->form_user->get_id());
		$pub->set_published(time());
		$pub->set_modified(time());
		$pub->set_hidden($hidden);
		$pub->set_from_date($from);
		$pub->set_to_date($to);
		$pub->set_target_groups($groups);
		$pub->set_target_users($users);

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
    	
    	$groups = GroupDataManager :: get_instance()->retrieve_classgroups();
    	
    	while ($group = $groups->next_result())
    	{
    		$receiver_choices[self :: PARAM_TARGET_GROUP_PREFIX.'-'.$group->get_id()] = Translation :: get('Group').': '.$group->get_name();
    	}
    	
    	$users = UserDataManager :: get_instance()->retrieve_users();
    	
    	while ($user = $users->next_result())
    	{
    		$receiver_options[self :: PARAM_TARGET_USER_PREFIX.'-'.$user->get_id()] = $user->get_fullname();
    	}
    	
    	return $receiver_options;
    }
    
    function set_system_announcement_publication($system_announcement_publication)
    {
    	$this->system_announcement_publication = $system_announcement_publication;
		$this->addElement('hidden','said');
		$this->addElement('hidden','action');
		$defaults['action'] = 'edit';
		$defaults['said'] = $system_announcement_publication->get_id();
		$defaults[SystemAnnouncementPublication :: PROPERTY_FROM_DATE] = $system_announcement_publication->get_from_date();
		$defaults[SystemAnnouncementPublication :: PROPERTY_TO_DATE] = $system_announcement_publication->get_to_date();
		if($defaults[SystemAnnouncementPublication :: PROPERTY_FROM_DATE] != 0)
		{
			$defaults[self :: PARAM_FOREVER] = 0;
		}
		$defaults[SystemAnnouncementPublication :: PROPERTY_HIDDEN] = $system_announcement_publication->is_hidden();
		$users = $system_announcement_publication->get_target_users();
		foreach($users as $user)
		{
			$defaults[self :: PARAM_TARGETS][self :: PARAM_TARGETS_TO][] = self :: PARAM_TARGET_USER_PREFIX . '-'.$user;
		}
		$groups = $system_announcement_publication->get_target_groups();
		foreach($groups as $index => $group)
		{
			$defaults[self :: PARAM_TARGETS][self :: PARAM_TARGETS_TO][] = self :: PARAM_TARGET_GROUP_PREFIX . '-'.$group;
		}
		
		if (count($users) > 0 || count($groups) > 0)
		{
			$defaults[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] = '1';
		}
		
		parent::setDefaults($defaults);
    }
    
    function update_learning_object_publication()
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
		$hidden = ($values[SystemAnnouncementPublication :: PROPERTY_HIDDEN] ? 1 : 0);
		
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

		$pub = $this->system_announcement_publication;
		$pub->set_modified(time());
		$pub->set_hidden($hidden);
		$pub->set_from_date($from);
		$pub->set_to_date($to);
		$pub->set_target_groups($groups);
		$pub->set_target_users($users);

		if ($pub->update())
		{
			return true;
		}
		else
		{
			return false;
		}
    }
}
?>