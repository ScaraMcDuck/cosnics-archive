<?php
/**
 * $Id: description_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage description
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/description.class.php';
/**
 * A form to create/update a description
 */
class DescriptionForm extends LearningObjectForm
{
	const TOTAL_PROPERTIES = 2;
	function setCsvValues($valuearray)
	{
		if(count($valuearray) == self :: TOTAL_PROPERTIES)
		{
			$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
			$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];	
			parent :: setValues($defaults);
			return true;
		}
		return false;
	}
	// Inherited
	function create_learning_object()
	{
		$object = new Description();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}

?>
