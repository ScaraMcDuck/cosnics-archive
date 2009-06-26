<?php
require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/portfolio_item.class.php';
/**
 * @package repository.learningobject
 * @subpackage portfolio
 */
class PortfolioItemForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
		$defaults[PortfolioItem :: PROPERTY_REFERENCE] = $valuearray[3];
		parent :: set_values($defaults);
	}

	function create_learning_object()
	{
		$object = new PortfolioItem();
		$object->set_reference($this->exportValue(PortfolioItem :: PROPERTY_REFERENCE));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_reference($this->exportValue(PortfolioItem :: PROPERTY_REFERENCE));
		return parent :: update_learning_object();
	}

	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('text',PortfolioItem :: PROPERTY_REFERENCE, Translation :: get('Reference'));
		$this->addElement('category');
	}

	function build_editing_form($object)
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('text',PortfolioItem :: PROPERTY_REFERENCE, Translation :: get('Reference'));
		$this->addElement('category');
	}

	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		$defaults[PortfolioItem :: PROPERTY_REFERENCE] = $object->get_reference();
		parent :: setDefaults($defaults);
	}
}
?>