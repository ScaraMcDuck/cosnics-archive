<?php
/**
 * @package repository.learningobject
 * @subpackage answer
 */

require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/question.class.php';

class QuestionForm extends LearningObjectForm 
{
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->add_select(Question :: PROPERTY_QUESTION_TYPE, Translation :: get('Type'), Question :: get_question_types());
    }
    // Inherited
    protected function build_editing_form()
	{
    	parent :: build_editing_form();
    	$this->add_select(Question :: PROPERTY_QUESTION_TYPE, Translation :: get('Question type'), Question :: get_question_types());
    }
	// Inherited
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
	
		if (isset ($lo))
		{
			$defaults[Question :: PROPERTY_QUESTION_TYPE] = $lo->get_type();
		}
		parent :: setDefaults($defaults);
	}

	function set_csv_values($valuearray)
	{	
		$defaults[Question :: PROPERTY_QUESTION_TYPE] = $valuearray[0];
		parent :: set_values($defaults);
	}

	// Inherited
	function create_learning_object()
	{ 
		$lo = $this->get_learning_object();
		$values = $this->exportValues();
		$lo->set_test($values[ComplexExercise :: PROPERTY_TEST]); 
		return parent :: create_learning_object();
	}
	// Inherited
	function update_learning_object()
	{
		$cloi = $this->get_learning_object();
		$values = $this->exportValues();
		$cloi->set_test($values[Question :: PROPERTY_QUESTION_TYPE]);
		return parent :: update_learning_object();
	}
}
?>