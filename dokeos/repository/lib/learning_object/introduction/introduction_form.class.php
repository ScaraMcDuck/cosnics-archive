<?php
/**
 * $Id: description_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage description
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/introduction.class.php';
/**
 * A form to create/update a introduction
 */
class IntroductionForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}

	// Inherited
	function create_learning_object()
	{
		$object = new Introduction();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}

?>
