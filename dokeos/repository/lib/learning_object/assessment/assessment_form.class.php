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
		$defaults[Assessment :: PROPERTY_ASSESSMENT_TYPE] = $object->get_assessment_type();
		parent :: setDefaults($defaults);
	}
	
	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(Assessment :: PROPERTY_ASSESSMENT_TYPE, Translation :: get('Assessment type'), Assessment :: get_types());
    	$this->addElement('category');
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_select(Assessment :: PROPERTY_ASSESSMENT_TYPE, Translation :: get('Assessment type'), Assessment :: get_types());
    	$this->addElement('category');
	}

	// Inherited
	function create_learning_object()
	{
		$object = new Assessment();
		$values = $this->exportValues();
		
		$ass_types = $object->get_types();
		$object->set_assessment_type($ass_types[$values[Assessment :: PROPERTY_ASSESSMENT_TYPE]]);
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
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
