<?php
require_once dirname(__FILE__).'/../../content_object_form.class.php';
require_once dirname(__FILE__).'/learning_path.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathForm extends ContentObjectForm
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
		$object = new LearningPath();
		$this->set_content_object($object);
		return parent :: create_content_object();
	}
}
?>
