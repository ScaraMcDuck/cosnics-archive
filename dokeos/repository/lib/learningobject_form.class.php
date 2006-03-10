<?php

/**
 * A form to create and edit a LearningObject
 * @package learningobject
 */
require_once dirname(__FILE__).'/../../claroline/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/condition/exactmatchcondition.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/quotamanager.class.php';
require_once dirname(__FILE__).'/categorymenu.class.php';
abstract class LearningObjectForm extends FormValidator
{
	/**
	 * The learning object
		 */
	protected $learningObject;

	protected $category;
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
	protected function build_create_form()
	{
		$this->addElement('text', 'title', 'Title');
		$this->addRule('title', 'Required', 'required');
		$select= & $this->addElement('select', 'category', get_lang('Category'), $this->get_categories());
		if (isset($this->category))
			$select->setSelected($this->category);
		$this->addRule('category', 'Category is required', 'required');
		$this->add_html_editor('description', 'Description');
	}
	/**
	 * Add a submit button to the form
	 */
	protected function add_submit_button()
	{
		$this->addElement('submit', 'submit', 'OK');
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
		$select= & $this->addElement('select', 'category', get_lang('Category'), $this->get_categories());
		$select->setSelected($this->learningObject->get_category_id());
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
	/**
	 * Get the categories defined in the users repository
	 * @return array The categories
	 */
	public function get_categories()
	{
		$categorymenu = new CategoryMenu(api_get_user_id());
		$renderer =& new HTML_Menu_ArrayRenderer();
		$categorymenu->render($renderer,'sitemap');
		$categories = $renderer->toArray();
		$category_choices = array();
		foreach($categories as $index => $category)
		{
			$prefix = '';
			if($category['level'] > 0)
			{
				$prefix = str_repeat('&nbsp;&nbsp;&nbsp;',$category['level']-1).'&mdash; ';
			}
			$category_choices[$category['id']] = $prefix.$category['title'];
		}
		return $category_choices;
	}
	function set_default_category ($category)
	{
		$this->category = $category;
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
	public static function factory($type, $formName, $method = 'post', $action = null)
	{
		$class = RepositoryDataManager :: type_to_class($type).'Form';
		require_once (dirname(__FILE__).'/learning_object/'.$type.'/form.class.php');
		return new $class ($formName, $method, $action);
	}

}
?>