<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/matching_question.class.php';
class MatchingQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->update_number_of_options_and_matches();
		$this->addElement('html','<div class="row"><div class="label"></div><div class="formw"><table style="width: 100%"><tr><td style="text-align:left; width: 50%; vertical-align: top;">');
		$this->add_options();
		$this->addElement('html','</td><td style="text-align:left; width: 50%; vertical-align: top;">');
		$this->add_matches();
		$this->addElement('html','</td></tr></table></div></div>');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->update_number_of_options_and_matches();
		$this->add_options();
		$this->add_matches();
	}
	function setDefaults($defaults = array ())
	{
		if(!$this->isSubmitted())
		{
			$object = $this->get_learning_object();
			if(!is_null($object))
			{
				$options = $object->get_options();
				foreach($options as $index => $option)
				{
					$defaults['option'][$index] = $option->get_value();
					$defaults['correct'][$index] = $option->is_correct();
				}
			}
		}
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
			$options[] = new MultipleChoiceQuestionOption($value,$values['correct'][$option_id]);
		}
		$object->set_options($options);
		return parent :: update_learning_object();
	}
	function validate()
	{
		if(isset($_POST['add_match']) || isset($_POST['remove_match']) || isset($_POST['remove_option']) || isset($_POST['add_option']))
		{
			return false;
		}
		return parent::validate();
	}
	/**
	 * Updates the session variables to keep track of the current number of
	 * options and matches.
	 * @todo This code needs some cleaning :)
	 */
	private function update_number_of_options_and_matches()
	{
		if(!$this->isSubmitted())
		{
			unset($_SESSION['mq_number_of_options']);
			unset($_SESSION['mq_skip_options']);
		}
		if(!isset($_SESSION['mq_number_of_options']))
		{
			$_SESSION['mq_number_of_options'] = 3;
		}
		if(!isset($_SESSION['mq_skip_options']))
		{
			$_SESSION['mq_skip_options'] = array();
		}
		if(isset($_POST['add_option']))
		{
			$_SESSION['mq_number_of_options'] = $_SESSION['mq_number_of_options']+1;
		}
		if(isset($_POST['remove_option']))
		{
			$indexes = array_keys($_POST['remove_option']);
			$_SESSION['mq_skip_options'][] = $indexes[0];
		}
		$object = $this->get_learning_object();
		if(!$this->isSubmitted() && !is_null($object))
		{
			$_SESSION['mq_number_of_options'] = $object->get_number_of_options();
		}
		if(!$this->isSubmitted())
		{
			unset($_SESSION['mq_number_of_matches']);
			unset($_SESSION['mq_skip_matches']);
		}
		if(!isset($_SESSION['mq_number_of_matches']))
		{
			$_SESSION['mq_number_of_matches'] = 2;
		}
		if(!isset($_SESSION['mq_skip_matches']))
		{
			$_SESSION['mq_skip_matches'] = array();
		}
		if(isset($_POST['add_match']))
		{
			$_SESSION['mq_number_of_matches'] = $_SESSION['mq_number_of_matches']+1;
		}
		if(isset($_POST['remove_match']))
		{
			$indexes = array_keys($_POST['remove_match']);
			$_SESSION['mq_skip_matches'][] = $indexes[0];
		}
		if(!$this->isSubmitted() && !is_null($object))
		{
			$_SESSION['mq_number_of_matches'] = $object->get_number_of_options();
		}
	}
	/**
	 * Adds the form-fields to the form to provide the possible options for this
	 * multiple choice question
	 */
	private function add_options()
	{
		$number_of_options = intval($_SESSION['mq_number_of_options']);
		$label = 1;
		$matches = array();
		$match_label = 'A';
		for($match_number = 0; $match_number<$_SESSION['mq_number_of_matches']; $match_number++)
		{
			if(!in_array($match_number,$_SESSION['mq_skip_matches']))
			{
				$matches[$match_number] = $match_label++;
			}
		}
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			if(!in_array($option_number,$_SESSION['mq_skip_options']))
			{
				$group = array();
				$group[] = $this->createElement('text','option['.$option_number.']', '', true,'size="40"');
				$group[] = $this->createElement('select','correct['.$option_number.']','',$matches);
				if($number_of_options - count($_SESSION['mq_skip_options']) > 2)
				{
					$group[] = $this->createElement('image','remove_option['.$option_number.']','/dokeos-lcms/main/img/delete.gif');
				}
				$this->addGroup($group,null,$label++);
				//$this->addRule('option['.$option_number.']',get_lang('ThisFieldIsRequired'),'required');
			}
		}
		$this->addElement('submit','add_option','+');
	}
	/**
	 * Adds the form-fields to the form to provide the possible matches for this
	 * matching question
	 */
	private function add_matches()
	{
		$number_of_matches = intval($_SESSION['mq_number_of_matches']);
		$label = 'A';
		for($match_number = 0; $match_number <$number_of_matches ; $match_number++)
		{
			if(!in_array($match_number,$_SESSION['mq_skip_matches']))
			{
				$group = array();
				//$group[] = $this->createElement('checkbox','correct['.$option_number.']');
				$group[] = $this->createElement('text','match['.$match_number.']', '', true,'size="40"');
				if($number_of_matches - count($_SESSION['mq_skip_matches']) > 2)
				{
					$group[] = $this->createElement('image','remove_match['.$match_number.']','/dokeos-lcms/main/img/delete.gif');
				}
				$this->addGroup($group,null,$label++);
				//$this->addRule('option['.$option_number.']',get_lang('ThisFieldIsRequired'),'required');
			}
		}
		$this->addElement('submit','add_match','+');
	}
}
?>