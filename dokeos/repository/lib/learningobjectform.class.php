<?php

/**
 * A form to create and edit a LearningObject.
 * @package repository.learningobject
 */

require_once dirname(__FILE__).'/../../claroline/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/quotamanager.class.php';
require_once dirname(__FILE__).'/categorymenu.class.php';
require_once dirname(__FILE__).'/learningobject.class.php';

abstract class LearningObjectForm extends FormValidator
{
	/**
	 * The learning object.
	 */
	private $learning_object;

	/**
	 * The ID of the category to create a learning object in.
	 */
	private $category;

	/**
	 * Whether or not the creation form has been built.
	 */
	private $creation_form_built;

	/**
	 * Constructor.
	 * @param string $formName The name to use in the form tag.
	 * @param string $method The method to use ('post' or 'get').
	 * @param string $action The URL to which the form should be submitted.
	 */
	protected function LearningObjectForm($formName, $method = 'post', $action = null)
	{
		parent :: FormValidator($formName, $method, $action);
		$this->creation_form_built = false;
	}

	/**
	 * Returns the learning object associated with this form.
	 * @return LearningObject The learning object, or null if none.
	 */
	protected function get_learning_object()
	{
		return $this->learning_object;
	}

	/**
	 * Sets the learning object associated with this form.
	 * @param LearningObject $object The learning object.
	 */
	protected function set_learning_object($object)
	{
		$this->learning_object = $object;
	}

	/**
	 * Builds a form to create a new learning object. Traditionally, you will
	 * extend this method so it adds fields for your learning object type's
	 * additional properties, and then calls the add_submit_button() method.
	 * @param LearningObject $default_learning_object The properties of this
	 *                                                learning object will be
	 *                                                used as default values
	 *                                                in the form.
	 */
	protected function build_creation_form($default_learning_object = null)
	{
		$this->set_learning_object($default_learning_object);
		$this->creation_form_built = true;
		$this->build_basic_form();
	}

	/**
	 * Builds a form to edit a learning object. Traditionally, you will extend
	 * this method so it adds fields for your learning object type's
	 * additional properties, and then calls the setDefaults() and
	 * add_submit_button() methods.
	 * @param LearningObject $learning_object The learning object to edit.
	 */
	protected function build_editing_form($learning_object)
	{
		$this->set_learning_object($learning_object);
		$this->category = $learning_object->get_parent_id();
		$this->creation_form_built = false;
		$this->build_basic_form();
		$this->addElement('hidden', LearningObject :: PROPERTY_ID);
	}

	/**
	 * Builds a form to create or edit a learning object. Creates fields for
	 * default learning object properties. The result of this function is equal
	 * to build_creation_form()'s, but that one may be overridden to extend the
	 * form.
	 * @param boolean $create True if creating a new object, false if editing
	 *                        an existing one.
	 */
	private function build_basic_form()
	{
		$this->addElement('text', LearningObject :: PROPERTY_TITLE, get_lang('Title'));
		$this->addRule(LearningObject :: PROPERTY_TITLE, get_lang('ThisFieldIsRequired'), 'required');
		if ($this->allows_category_selection())
		{
			$select = & $this->addElement('select', LearningObject :: PROPERTY_PARENT_ID, get_lang('Category'),$this->get_categories());
			if (isset ($this->category))
			{
				$select->setSelected($this->category);
			}
			$this->addRule(LearningObject :: PROPERTY_PARENT_ID, get_lang('ThisFieldIsRequired'), 'required');
		}
		$this->add_html_editor(LearningObject :: PROPERTY_DESCRIPTION, get_lang('Description'));
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
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults[LearningObject :: PROPERTY_ID] = $lo->get_id();
			$defaults[LearningObject :: PROPERTY_TITLE] = $lo->get_title();
			$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $lo->get_description();
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
	 * Gets the default category.
	 * @return int The category ID.
	 */
	function get_default_category()
	{
		return $this->category;
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
	 * Creates a learning object from the submitted form values. Traditionally,
	 * you override this method to ensure that the form's learning object is
	 * set to the object that is to be created, and call the super method.
	 * @param int $owner The user ID of the learning object's owner.
	 * @return LearningObject The newly created learning object.
	 */
	function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$object = & $this->get_learning_object();
		$object->set_owner_id($owner);
		$object->set_title($values[LearningObject :: PROPERTY_TITLE]);
		$object->set_description($values[LearningObject :: PROPERTY_DESCRIPTION]);
		if ($this->allows_category_selection())
		{
			$object->set_parent_id($values[LearningObject :: PROPERTY_PARENT_ID]);
		}
		$object->create();
		return $object;
	}

	/**
	 * Updates a learning object with the submitted form values. Traditionally,
	 * you override this method to first set values for the necessary
	 * additional learning object properties, and then call the super method.
	 * @param LearningObject $learning_object The object to update.
	 * @return boolean True if the update succeeded, false otherwise.
	 */
	function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values[LearningObject :: PROPERTY_TITLE]);
		$object->set_description($values[LearningObject :: PROPERTY_DESCRIPTION]);
		if ($this->allows_category_selection())
		{
			$parent = $values[LearningObject :: PROPERTY_PARENT_ID];
			if ($parent != $object->get_parent_id())
			{
				if ($object->move_allowed($parent))
				{
					$object->set_parent_id($parent);
				}
				else
				{
					/*
					 * TODO: Make this more meaningful, e.g. by returning error
					 * constants instead of booleans, like
					 * LearningObjectForm :: SUCCESS (not implemented).
					 */
					return false;
				}
			}
		}
		return $object->update();
	}

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
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_form.class.php';
		return new $class ($formName, $method, $action);
	}

	function display()
	{
		$quotamanager = new QuotaManager(api_get_user_id());
		if($this->creation_form_built && $quotamanager->get_available_database_space() <= 0)
		{
			Display::display_error_message(get_lang('DatabaseQuotaExceeded'));
		}
		else
		{
			parent::display();
		}
	}

	private function allows_category_selection()
	{
		$lo = $this->get_learning_object();
		return ($this->creation_form_built || !isset($lo) || $lo->get_parent_id() > 0);
	}
}
?>