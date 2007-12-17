<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/fill_in_blanks_question.class.php';
class FillInBlanksQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->build_fill_in_blanks_form();
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->build_fill_in_blanks_form();
	}
	private function build_fill_in_blanks_form()
	{
		$this->addElement('textarea',FillInBlanksQuestion :: PROPERTY_ANSWER, get_lang('Answer'),'rows="5" cols="60"');
		$this->addRule(FillInBlanksQuestion :: PROPERTY_ANSWER,get_lang('ThisFieldIsRequired'),'required');
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
	function setCsvValues($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];
		$defaults[FillInBlanksQuestion :: PROPERTY_ANSWER] = $valuearray[2];	
		parent :: setValues($defaults);		
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
