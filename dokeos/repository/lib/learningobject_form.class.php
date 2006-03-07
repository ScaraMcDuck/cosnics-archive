<?php
/**
 * A form to create and edit a LearningObject
 * @package learningobject
 */
require_once dirname(__FILE__) . '/../../claroline/inc/lib/formvalidator/FormValidator.class.php'; 
class LearningObjectForm extends FormValidator
{
	/**
	 * The learning object
	 */
	protected $learningObject;
	/**
	 * Constructor
	 */
	protected function LearningObjectForm($formName, $method = 'post', $action = null)
	{
		parent :: FormValidator($formName, $method, $action);
	}
	/**
	 * Build a form to create a new learning object
	 */
	protected function get_learning_object()
	{
		return $this->learningObject;
	}		
	
	protected function build_create_form()
	{
		$this->addElement('text', 'title', 'Title');
		$this->addRule('title', 'Required', 'required');
		$this->add_html_editor('description', 'Description');
	}
	/**
	 * Add a submit button to the form
	 */
	protected function addSubmitButton()
	{
		$this->addElement('submit', 'submit', 'Ok');
	}
	/**
	 * Build a form to edit a learning object
	 * @param LearningObject The learning object to edit
	 */
	protected function build_edit_form($learningObject)
	{
		$this->learningObject = $learningObject;
		$this->addElement('text', 'title', 'Title');
		$this->addRule('title', 'Required', 'required');
		$this->add_html_editor('description', 'Description');
		$this->addElement('hidden', 'id');
	}
	/**
	 * Set default values
	 */
	public function setDefaults($defaults = array ())
	{
		if (isset ($this->learningObject))
		{
			$defaults['id'] = $this->learningObject->get_id();
			$defaults['title'] = $this->learningObject->get_title();
			$defaults['description'] = $this->learningObject->get_description();
		}
		parent :: setDefaults($defaults);
	}
	/**
	 * Create a learning object from the submitted form values
	 */
	protected function create_learning_object()
	{
		return;
	}
	/**
	 * Update a learning object with the submitted form values
	 */
	protected function update_learning_object()
	{
		return;
	}
	/**
	 *
	 */
	public static function factory($type,$formName,$method='post',$action = null)
	{
		$class = ucfirst($type).'Form';
		require_once(dirname(__FILE__).'/learning_object/'.strtolower($type).'/form.class.php');
		return new $class($formName,$method,$action);
	}
}
?>