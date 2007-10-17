<?php
/**
 * @package application.lib.profiler
 */
require_once dirname(__FILE__).'/profilepublication.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';
require_once api_get_path(SYS_CODE_PATH).'/../plugin/html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class ProfilePublicationForm extends FormValidator
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
    function ProfilePublicationForm($learning_object, $form_user, $action)
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

		$this->addElement('submit', 'submit', get_lang('Ok'));
    }

	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_learning_object_publication()
    {
		$values = $this->exportValues();

		$pub = new ProfilePublication();
		$pub->set_profile($this->learning_object->get_id());
		$pub->set_publisher($this->form_user->get_user_id());
		$pub->set_published(time());

		if ($pub->create())
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