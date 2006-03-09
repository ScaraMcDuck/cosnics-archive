<?php
/**
 * A form to create and edit a LearningObject
 * @package learningobject
 */
require_once dirname(__FILE__) . '/../../claroline/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/condition/exactmatchcondition.class.php';
require_once dirname(__FILE__) . '/repositorydatamanager.class.php';
abstract class LearningObjectForm extends FormValidator
{
	/**
	 * The learning object
 	 */
	protected $learningObject;
	/**
	 * Constructor
	 * @param  string $formName The name to use in the form-tag
	 * @param string $method The method to use ('post' or 'get')
	 * @param string $action The URL to which the form should be submitted
	 */
	protected function LearningObjectForm($formName, $method = 'post', $action = null)
	{
		parent :: FormValidator($formName, $method, $action);
	}
	/**
	 * Get the learning object
	 */
	protected function get_learning_object()
	{
		return $this->learningObject;
	}
	/**
	 * Build a form to create a new learning object
	 */
	protected function build_create_form($type)
	{
		$this->addElement('header', 'category_type', ucfirst($type));
		$this->addElement('text', 'title', 'Title');
		$this->addRule('title', 'Required', 'required');
		$this->addElement('select', 'category', get_lang('Category'), $this->getCategories());
		$this->addRule('category', 'Category is required', 'required');
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
		$this->addElement('text', 'category', 'Category');
		$this->addRule('category', 'Category is required', 'required');
		$this->add_html_editor('description', 'Description');
		$this->addElement('hidden', 'id');
	}
	/**
	 * Set default values
	 * @param array $defaults Default values for this form
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
	public function getCategories()
	{
		$datamanager = RepositoryDataManager::get_instance();
		$condition = new ExactMatchCondition('owner',api_get_user_id());
		$categories = $datamanager->retrieve_learning_objects('category',$condition);
		$category_choices = array();
		foreach($categories as $index => $category)
		{
			$category_choices[$category->get_id()] = $category->get_title();
		}
		return $category_choices;
	}
	/**
	 * Create a learning object from the submitted form values
	 * @param int $owner The user-id of the owner of the learning object
	 */
	abstract function create_learning_object($owner);
	/**
	 * Update a learning object with the submitted form values
	 * @param LearningObject $learning_object The object to update
	 */
	abstract function update_learning_object(& $learning_object);
	/**
	 * Create a form object to manage a learning object
	 * @param string $type The type of the learning object
	 * @param string $formName The name to use in the form-tag
	 * @param string $method The method to use ('post' or 'get')
	 * @param string $action The URL to which the form should be submitted
	 */
	public static function factory($type,$formName,$method='post',$action = null)
	{
		$class = RepositoryDataManager :: type_to_class($type).'Form';
		require_once(dirname(__FILE__).'/learning_object/'.strtolower($type).'/form.class.php');
		return new $class($formName,$method,$action);
	}
}
?>