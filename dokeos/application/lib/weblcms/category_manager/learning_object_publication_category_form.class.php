<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * Form for creating and updating a learning object publication category
 */
class LearningObjectPublicationCategoryForm extends FormValidator
{
	const PARAM_TITLE = 'title';
	const PARAM_CATEGORY = 'parent';
	/**
	 * The category manager in which this form is created.
	 */
	private $parent;
	/**
	 * The category edited or created by this form
	 */
	private $category;
	/**
	 * Constructor
	 * @param LearningObjectPublicationCategoryManager $parent The category
	 * manager in which this form is created.
	 * @param string $formName
	 * @param string $method
	 * @param string $action
	 */
	function LearningObjectPublicationCategoryForm($parent, $formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
		$this->parent = $parent;
		// Initialize it with the root category (mostly to avoid any null value when no category is set later on)
		$this->category = 0;
	}
	/**
	 * Add the necessary elements to this form so it can be used to create a new
	 * learning object publication category
	 */
	function build_creation_form()
	{
		$this->addElement('text', self :: PARAM_TITLE, Translation :: get('Title'));
		$this->addRule(self :: PARAM_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
		$categories = $this->parent->get_categories(true);
		$this->addElement('select', self :: PARAM_CATEGORY, Translation :: get('Category'), $categories);
		$this->addElement('submit', 'submit', Translation :: get('Ok'));
	}
	/**
	 * Add the necessary elements to this form so it can be used to edit an
	 * existing learning object publication category
	 */
	function build_editing_form($category)
	{
		$this->category = $category;
		$this->build_creation_form();
		$this->setDefaults();
	}
	/**
	 * Sets the default values of the form
	 * @param array $defaults
	 */
	function setDefaults($defaults = array ())
	{
		if (isset($this->category))
		{
			$defaults[self :: PARAM_TITLE] = $this->category->get_title();
			$defaults[self :: PARAM_CATEGORY] = $this->category->get_parent_category_id();
		}
		parent :: setDefaults($defaults);
	}
	/**
	 * Gets the title given in the form
	 * @return string The title.
	 */
	function get_category_title()
	{
		return $this->exportValue(self :: PARAM_TITLE);
	}
	/**
	 * Gets the id of the parent category
	 * @return int The id.
	 */
	function get_category_parent()
	{
		$cat = $this->exportValue(self :: PARAM_CATEGORY);
		if (!isset($cat)) {
			$cat = 0;
		}
		return $cat;
	}
}
?>