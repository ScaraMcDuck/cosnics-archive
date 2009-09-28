<?php
require_once dirname(__FILE__).'/../../content_object_form.class.php';
require_once dirname(__FILE__).'/scorm_item.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class ScormItemForm extends ContentObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}

	function create_content_object()
	{
		$object = new ScormItem();
		$this->set_content_object($object);
		return parent :: create_content_object();
	}
	
	function update_content_object()
	{
		$object = $this->get_content_object();
		return parent :: update_content_object();
	}
	
	function build_creation_form($default_content_object = null)
	{
		parent :: build_creation_form($default_content_object);
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
		$object = $this->get_content_object();
		parent :: setDefaults($defaults);
	}
		
}
?>
