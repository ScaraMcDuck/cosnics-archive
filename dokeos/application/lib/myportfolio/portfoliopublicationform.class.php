<?php
/**
 * @package application.lib.portfolio
 */
require_once dirname(__FILE__).'/portfoliopublication.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_plugin_path().'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class PortfolioPublicationForm extends FormValidator
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
    function PortfolioPublicationForm($learning_object, $form_user, $action)
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
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		//$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }

	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_learning_object_publication()
    {
		$values = $this->exportValues();

		$pub = new PortfolioPublication();
		$pub->set_portfolio_item($this->learning_object->get_id());
		$pub->set_publisher($this->form_user->get_id());
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