<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/personal_message_publication.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_plugin_path().'html2text/class.html2text.inc';
require_once Path :: get_group_path(). 'lib/group_data_manager.class.php';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class PersonalMessagePublicationForm extends FormValidator
{
   /**#@+
    * Constant defining a form parameter
 	*/

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

	//private $publication;

	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
	//function PersonalMessagePublicationForm($learning_object, $publication = null, $form_user, $action)
    function PersonalMessagePublicationForm($learning_object, $form_user, $action)
    {
		parent :: __construct('publish', 'post', $action);
		$this->learning_object = $learning_object;
		//$this->publication = $publication;
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
		parent :: setDefaults($defaults);
    }
	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
//    	$publication = $this->publication;
    	$recipients = array ();
//    	if ($publication)
//    	{
//			$publication = $this->publication;
//			$recip = $publication->get_publication_sender();
//			$recipient = array ();
//			$recipient['id'] = $recip->get_id();
//			$recipient['class'] = 'type type_user';
//			$recipient['title'] = $recip->get_username();
//			$recipient['description'] = $recip->get_lastname() . ' ' . $recip->get_firstname();
//			$recipients[$recipient['id']] = $recipient;
//    	}

		$url = Path :: get(WEB_PATH).'application/lib/personal_messenger/xml_feed.php';
		$locale = array ();
		$locale['Display'] = Translation :: get('SelectRecipients');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$hidden = false;
		$elem = $this->addElement('element_finder', 'recipients', Translation :: get('Recipients'), $url, $locale, $recipients);
		$elem->excludeElements(array($this->form_user->get_id()));
		$elem->setDefaultCollapsed(false);

		//$this->addElement('submit', 'submit', Translation :: get('Ok'));
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Send'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

	private $failures = 0;
	private $sent_users = array();
	
	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_learning_object_publication($extra_rec = array())
    {
		$values = $this->exportValues();
		$pmdm = PersonalMessengerDataManager :: get_instance();
		//dump($values);
		$recipients = array_merge($extra_rec, $values['recipients']);
		
		foreach ($recipients as $recipient)
		{ 
			$split = explode("|", $recipient);
			$type = $split[0];
			$recip = $split[1];

			if($type == 'user')
			{
				$users[] = $recip;
			}
			else
			{
				$grus = GroupDataManager :: get_instance()->retrieve_group_rel_users(new EqualityCondition('group_id', $recip));
				while($gru = $grus->next_result())
					$users[] = $gru->get_user_id();
			}
			
			foreach($users as $user)
			{ 
				if ($user != $this->form_user->get_id() && !in_array($user, $this->sent_users))
				{
					$this->send_to_recipient($user);
				}
				else
				{
					$this->failures++;
				}
			}
		}

		if ($this->failures > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
    }
    
    private function send_to_recipient($recip)
    {
    	$sender_pub = new PersonalMessagePublication();
		$sender_pub->set_personal_message($this->learning_object->get_id());
		$sender_pub->set_recipient($recip);
		$sender_pub->set_published(time());
		$sender_pub->set_user($this->form_user->get_id());
		$sender_pub->set_sender($this->form_user->get_id());
		$sender_pub->set_status('0');

		if ($sender_pub->create())
		{
			$recipient_pub = new PersonalMessagePublication();
			$recipient_pub->set_personal_message($this->learning_object->get_id());
			$recipient_pub->set_recipient($recip);
			$recipient_pub->set_published(time());
			$recipient_pub->set_user($recip);
			$recipient_pub->set_sender($this->form_user->get_id());
			$recipient_pub->set_status('1');
			if ($recipient_pub->create())
			{
				$this->sent_users[] = $recip;
			}
			else
			{
				$this->failures++;
			}
		}
		else
		{
			$this->failures++;
		}
		
    }
}
?>