<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/forum.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumForm extends LearningObjectForm
{
	function setCsvValues($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];	
		parent :: setValues($defaults);
			
	}
	function create_learning_object()
	{
		$object = new Forum();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>
