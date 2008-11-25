<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/fill_in_blanks_question.class.php';
class FillInBlanksQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->build_fill_in_blanks_form();
		$this->addElement('category');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->build_fill_in_blanks_form();
		$this->addElement('category');
	}
	private function build_fill_in_blanks_form()
	{
		$this->addElement('textarea',FillInBlanksQuestion :: PROPERTY_ANSWER, Translation :: get('Answer'),'rows="5" cols="60"');
		$this->addRule(FillInBlanksQuestion :: PROPERTY_ANSWER,Translation :: get('ThisFieldIsRequired'),'required');
	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[FillInBlanksQuestion :: PROPERTY_ANSWER] = $lo->get_answer();
		}
		parent :: setDefaults($defaults);
	}

	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		$defaults[FillInBlanksQuestion :: PROPERTY_ANSWER] = $valuearray[3];
		parent :: set_values($defaults);			
	}

	function create_learning_object()
	{
		$object = new FillInBlanksQuestion();
		$object->set_answer($this->exportValue(FillInBlanksQuestion :: PROPERTY_ANSWER));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_answer($this->exportValue(FillInBlanksQuestion :: PROPERTY_ANSWER));
		return parent :: update_learning_object();
	}
}
?>
