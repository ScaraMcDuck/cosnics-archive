<?php
/**
 * @package repository.learningobject
 * @subpackage answer
 */

require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/question.class.php';

class QuestionForm extends LearningObjectForm 
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
		$object = new Question();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>