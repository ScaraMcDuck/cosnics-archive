<?php
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/formvalidator/FormValidator.class.php';
class LearningObjectPublicationCategoryForm extends FormValidator
{
	private $category;
	
	function LearningObjectPublicationCategoryForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	
	function build_creation_form()
	{
		$this->addElement('text', 'title', get_lang('Title'));
		$this->addRule('title', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('submit', 'submit', get_lang('Ok'));
	}
	
	function build_modification_form($category)
	{
		$this->category = $category;				
		$this->build_creation_form();
		$this->setDefaults();
	}
	
	function setDefaults($defaults = array ())
	{
		if (isset($this->category))
		{
			$defaults['title'] = $this->category->get_title();
		}
		parent :: setDefaults($defaults);
	}
}
?>