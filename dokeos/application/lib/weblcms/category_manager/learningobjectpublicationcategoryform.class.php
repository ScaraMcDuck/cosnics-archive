<?php
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/formvalidator/FormValidator.class.php';
class LearningObjectPublicationCategoryForm extends FormValidator
{
	const PARAM_TITLE = 'title';
	const PARAM_CATEGORY = 'parent';
	
	private $parent;
	
	private $category;
	
	function LearningObjectPublicationCategoryForm($parent, $formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
		$this->parent = $parent;
	}
	
	function build_creation_form()
	{
		$this->addElement('text', self :: PARAM_TITLE, get_lang('Title'));
		$this->addRule(self :: PARAM_TITLE, get_lang('ThisFieldIsRequired'), 'required');
		$categories = $this->parent->get_categories(true);
		$this->addElement('select', self :: PARAM_CATEGORY, get_lang('Category'), $categories);
		$this->addElement('submit', 'submit', get_lang('Ok'));
	}
	
	function build_editing_form($category)
	{
		$this->category = $category;				
		$this->build_creation_form();
		$this->setDefaults();
	}
	
	function setDefaults($defaults = array ())
	{
		if (isset($this->category))
		{
			$defaults[self :: PARAM_TITLE] = $this->category->get_title();
			$defaults[self :: PARAM_CATEGORY] = $this->category->get_parent_category_id();
		}
		parent :: setDefaults($defaults);
	}
	
	function get_category_title()
	{
		return $this->exportValue(self :: PARAM_TITLE);
	}

	function get_category_parent()
	{
		return $this->exportValue(self :: PARAM_CATEGORY);
	}
}
?>