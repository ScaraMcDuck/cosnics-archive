<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/open_question.class.php';
/**
 * This class represents a form to create or update open questions
 */
class OpenQuestionForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		$defaults[OpenQuestion :: PROPERTY_QUESTION_TYPE] = $valuearray[3];

		parent :: set_values($defaults);			
	}
	
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if ($object != null) {
			$defaults[OpenQuestion :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
		}
			
		parent :: setDefaults($defaults);
	}

	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(OpenQuestion :: PROPERTY_QUESTION_TYPE, Translation :: get('OpenQuestionType'), OpenQuestion :: get_types());
    	$this->addElement('category');
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(OpenQuestion :: PROPERTY_QUESTION_TYPE, Translation :: get('OpenQuestionType'), OpenQuestion :: get_types());
    	$this->addElement('category');
	}

	// Inherited
	function create_learning_object()
	{
		$object = new OpenQuestion();
		
		$values = $this->exportValues();
		$object->set_question_type($values[OpenQuestion :: PROPERTY_QUESTION_TYPE]);
		
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		
		$values = $this->exportValues();
		$object->set_question_type($values[OpenQuestion :: PROPERTY_QUESTION_TYPE]);
		
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>
