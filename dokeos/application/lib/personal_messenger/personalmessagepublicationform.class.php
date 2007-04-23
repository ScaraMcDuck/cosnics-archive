<?php
/**
 * @package application.personal_messenger
 */
require_once dirname(__FILE__).'/personalmessagepublication.class.php';
require_once dirname(__FILE__).'../../../users/lib/usersdatamanager.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/html2text.class.php';
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
	
	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function PersonalMessagePublicationForm($learning_object, $form_user, $action)
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
		parent :: setDefaults($defaults);
    }
	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
    	
		$this->addElement('text', PersonalMessagepublication :: PROPERTY_RECIPIENT, get_lang('Recipient'));
		$this->addElement('submit', 'submit', get_lang('Ok'));
    }

	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_learning_object_publication()
    {
		$values = $this->exportValues();

		$pmdm = PersonalMessengerDataManager :: get_instance();
		
		$udm = UsersDataManager :: get_instance();
		$recipient_id = retrieve_user_by_username($values[PersonalMessagePublication :: PROPERTY_RECIPIENT]);
		
		$pub = new PersonalMessagePublication();
		$pub->set_personal_message($this->learning_object->get_id());
		$pub->set_recipient($values[PersonalMessagePublication :: PROPERTY_RECIPIENT]);
		$pub->set_published(time());
		$pub->set_publisher($this->form_user);
		$pub->set_sender($this->form_user);
		$pub->set_status('0');
		$pub->create();
		
		return $pub;
    }
}
?>