<?php

/**
 * A form to create and edit a LearningObject.
 * @package learningobject
 */

require_once dirname(__FILE__).'/../../claroline/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/quotamanager.class.php';
require_once dirname(__FILE__).'/categorymenu.class.php';

abstract class LearningObjectForm extends FormValidator
{
	/**
	 * The learning object.
	 */
	protected $learningObject;

	/**
	 * The ID of the category to create a learning object in.
	 */
	protected $category;

	/**
	 * Constructor.
	 * @param  string $formName The name to use in the form tag.
	 * @param string $method The method to use ('post' or 'get').
	 * @param string $action The URL to which the form should be submitted.
	 */
	protected function LearningObjectForm($formName, $method = 'post', $action = null)
	{
		parent :: FormValidator($formName, $method, $action);
	}
	
	/**
	 * Returns the learning object.
	 */
	protected function get_learning_object()
	{
		return $this->learningObject;
	}
	
	/**
	 * Builds a form to create a new learning object. Traditionally, you will
	 * extend this method so it adds fields for your learning object type's
	 * additional properties, and then calls the add_submit_button() method.
	 */
	protected function build_creation_form()
	{
		$this->build_basic_form();
	}
	
	/**
	 * Builds a form to edit a learning object. Traditionally, you will extend
	 * this method so it adds fields for your learning object type's
	 * additional properties, and then calls the setDefaults() and
	 * add_submit_button() methods.
	 * @param LearningObject $learningObject The learning object to edit.
	 */
	protected function build_editing_form($learningObject)
	{
		$this->learningObject = $learningObject;
		$this->category = $this->learningObject->get_parent_id();
		$this->build_basic_form();
		$this->addElement('hidden', 'id');
	}
	
	/**
	 * Builds a form to create or edit a learning object. Creates fields for
	 * default learning object properties. The return value of this function
	 * is equal to build_creation_form()'s, but that one may be overridden to
	 * extend the form.
	 */
	private function build_basic_form()
	{
		$this->addElement('text', 'title', get_lang('Title'));
		$this->addRule('title', get_lang('ThisFieldIsRequired'), 'required');
		$select = & $this->addElement('select', 'category', get_lang('Category'), $this->get_categories());
		if (isset ($this->category))
		{
			$select->setSelected($this->category);
		}
		$this->addRule('category', get_lang('ThisFieldIsRequired'), 'required');
		$this->add_html_editor('description', get_lang('Description'));
	}
	
	/**
	 * Adds a submit button to the form.
	 */
	protected function add_submit_button()
	{
		$this->addElement('submit', 'submit', get_lang('Ok'));
	}
	
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
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
	 * Gets the categories defined in the user's repository.
	 * @return array The categories.
	 */
	function get_categories()
	{
		$categorymenu = new CategoryMenu(api_get_user_id());
		$renderer = & new HTML_Menu_ArrayRenderer();
		$categorymenu->render($renderer, 'sitemap');
		$categories = $renderer->toArray();
		$category_choices = array ();
		foreach ($categories as $index => $category)
		{
			$prefix = '';
			if ($category['level'] > 0)
			{
				$prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $category['level'] - 1).'&mdash; ';
			}
			$category_choices[$category['id']] = $prefix.$category['title'];
		}
		return $category_choices;
	}
	
	/**
	 * Sets the default category.
	 * @param int $category The category ID.
	 */
	function set_default_category($category)
	{
		$this->category = $category;
	}
	
	/**
	 * Creates a learning object from the submitted form values.
	 * @param int $owner The user ID of the owner of the learning object.
	 */
	abstract function create_learning_object($owner);
	
	/**
	 * Updates a learning object with the submitted form values.
	 * @param LearningObject $learning_object The object to update.
	 */
	abstract function update_learning_object(& $learning_object);
	
	/**
	 * Creates a form object to manage a learning object.
	 * @param string $type The type of the learning object.
	 * @param string $formName The name to use in the form tag.
	 * @param string $method The method to use ('post' or 'get').
	 * @param string $action The URL to which the form should be submitted.
	 */
	static function factory($type, $formName, $method = 'post', $action = null)
	{
		$class = RepositoryDataManager :: type_to_class($type).'Form';
		require_once (dirname(__FILE__).'/learning_object/'.$type.'/form.class.php');
		return new $class ($formName, $method, $action);
	}
}
?>