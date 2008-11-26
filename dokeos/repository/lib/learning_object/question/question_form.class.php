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
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(Question :: PROPERTY_QUESTION_TYPE, Translation :: get('Question type'), Question :: get_question_types());
    	$this->addElement('category');
    }
    // Inherited
    protected function build_editing_form()
	{
    	parent :: build_editing_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(Question :: PROPERTY_QUESTION_TYPE, Translation :: get('Question type'), Question :: get_question_types());
    	$this->addElement('category');
    }
	// Inherited
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
	
		if (isset ($lo))
		{
			$defaults[Question :: PROPERTY_QUESTION_TYPE] = $lo->get_question_type();
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
		$lo = new Question();
		$this->set_learning_object($lo);
		$this->set_question_type($lo);
		return parent :: create_learning_object();
	}
	// Inherited
	function update_learning_object()
	{
		$lo = $this->get_learning_object();
		$this->set_question_type($lo);
		return parent :: update_learning_object();
	}
	
	function set_question_type($lo) 
	{
		$values = $this->exportValues();
		$question_types = $lo->get_question_types();
		$lo->set_question_type($question_types[$values[Question :: PROPERTY_QUESTION_TYPE]]);
	}
}
?>