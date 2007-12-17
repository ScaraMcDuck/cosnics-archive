<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/open_question.class.php';
/**
 * This class represents a form to create or update open questions
 */
class OpenQuestionForm extends LearningObjectForm
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
		$object = new OpenQuestion();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>
