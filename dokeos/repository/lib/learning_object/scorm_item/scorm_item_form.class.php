<?php
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/learning_path_item.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class ScormItemForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}

	function create_learning_object()
	{
		$object = new ScormItem();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		return parent :: update_learning_object();
	}
	
	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form($default_learning_object);
		/*$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('category');*/
	}
	
	function build_editing_form($object)
	{
		parent :: build_editing_form();
		/*$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('category');*/
	}
	
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		parent :: setDefaults($defaults);
	}
		
}
?>
