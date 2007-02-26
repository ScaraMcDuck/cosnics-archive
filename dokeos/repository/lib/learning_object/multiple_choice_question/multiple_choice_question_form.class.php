<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/multiple_choice_question.class.php';
class MultipleChoiceQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_options();
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
	}
	function setDefaults($defaults = array ())
	{
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$object = new MultipleChoiceQuestion();
		$this->set_learning_object($object);
		$values = $this->exportValues();
		$options = array();
		foreach($values['option'] as $option_id => $value)
		{
			$options[] = new MultipleChoiceQuestionOption($value);
		}
		$object->set_options($options);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$options = array();
		foreach($values['option'] as $option_id => $value)
		{
			$options[] = new MultipleChoiceQuestionOption($value);
		}
		$object->set_options($options);
		return parent :: update_learning_object();
	}
	function validate()
	{
		if(isset($_POST['add']) || isset($_POST['remove']))
		{
			return false;
		}
		return parent::validate();
	}
	/**
	 * Adds the form-fields to the form to provide the possible options for this
	 * multiple choice question
	 */
	private function add_options()
	{
		if(!$this->isSubmitted())
		{
			unset($_SESSION['mc_number_of_options']);
			unset($_SESSION['mc_skip_options']);
		}
		if(!isset($_SESSION['mc_number_of_options']))
		{
			$_SESSION['mc_number_of_options'] = 3;
		}
		if(!isset($_SESSION['mc_skip_options']))
		{
			$_SESSION['mc_skip_options'] = array();
		}
		if(isset($_POST['add']))
		{
			$_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options']+1;
		}
		if(isset($_POST['remove']))
		{
			$indexes = array_keys($_POST['remove']);
			$_SESSION['mc_skip_options'][] = $indexes[0];
		}
		$number_of_options = intval($_SESSION['mc_number_of_options']);
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			if(!in_array($option_number,$_SESSION['mc_skip_options']))
			{
				$group = array();
				$group[] = $this->createElement('checkbox','correct['.$option_number.']');
				$group[] = $this->createElement('text','option['.$option_number.']', '', true,'size="40"');
				if($number_of_options - count($_SESSION['mc_skip_options']) > 2)
				{
					$group[] = $this->createElement('image','remove['.$option_number.']','/dokeos-lcms/main/img/delete.gif');
				}
				$this->addGroup($group,null,'');
				//$this->addRule('option['.$option_number.']',get_lang('ThisFieldIsRequired'),'required');
			}
		}
		$this->addElement('submit','add','+');
	}
}
?>