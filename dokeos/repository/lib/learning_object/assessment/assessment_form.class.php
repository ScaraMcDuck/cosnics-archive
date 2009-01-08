<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/assessment.class.php';
/**
 * This class represents a form to create or update assessment
 */
class AssessmentForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		$defaults[Assessment :: PROPERTY_ASSESSMENT_TYPE] = $valuearray[3];

		parent :: set_values($defaults);			
	}
	
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if ($object != null) {
			$defaults[Assessment :: PROPERTY_ASSESSMENT_TYPE] = $object->get_assessment_type();
			$defaults[Assessment :: PROPERTY_MAXIMUM_TIMES_TAKEN] = $object->get_maximum_times_taken();
			$defaults[Assessment :: PROPERTY_QUESTIONS_PER_PAGE] = $object->get_questions_per_page();
		}
			
		parent :: setDefaults($defaults);
	}
	
	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(Assessment :: PROPERTY_ASSESSMENT_TYPE, Translation :: get('AssessmentType'), Assessment :: get_types());
    	$this->add_textfield(Assessment :: PROPERTY_MAXIMUM_TIMES_TAKEN, Translation :: get('MaximumPerStudent')); 
    	$this->add_textfield(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, Translation :: get('QuestionsPerPage'));
    	$this->addElement('category');
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(Assessment :: PROPERTY_ASSESSMENT_TYPE, Translation :: get('AssessmentType'), Assessment :: get_types());
    	$this->add_textfield(Assessment :: PROPERTY_MAXIMUM_TIMES_TAKEN, Translation :: get('MaximumPerStudent'));
    	$this->add_textfield(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, Translation :: get('QuestionsPerPage')); 
    	$this->addElement('category');
	}

	// Inherited
	function create_learning_object()
	{
		$object = new Assessment();
		$values = $this->exportValues();
		if (isset($values[Assessment :: PROPERTY_MAXIMUM_TIMES_TAKEN]))
			$object->set_maximum_times_taken($values[Assessment :: PROPERTY_MAXIMUM_TIMES_TAKEN]);
		else
			$object->set_maximum_times_taken(0);

		if (isset($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]))
			$object->set_questions_per_page($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]);
		else
			$object->set_questions_per_page(0);
			
		$ass_types = $object->get_types();
		$object->set_assessment_type($ass_types[$values[Assessment :: PROPERTY_ASSESSMENT_TYPE]]);
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		if (isset($values[Assessment :: PROPERTY_MAXIMUM_TIMES_TAKEN]))
			$object->set_maximum_times_taken($values[Assessment :: PROPERTY_MAXIMUM_TIMES_TAKEN]);
		else
			$object->set_maximum_times_taken(0);

		if (isset($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]))
			$object->set_questions_per_page($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]);
		else
			$object->set_questions_per_page(0);
		
		$ass_types = $object->get_types();
		$value = $values[Assessment :: PROPERTY_ASSESSMENT_TYPE];
		if (is_numeric($value))
		{
			$object->set_assessment_type($ass_types[$value]);
		}
		else
		{
			$object->set_assessment_type($value);
		}
		$this->set_learning_object($object);
		return parent :: update_learning_object();
	}
}
?>
