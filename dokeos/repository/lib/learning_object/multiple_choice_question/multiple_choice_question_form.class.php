<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/multiple_choice_question.class.php';
class MultipleChoiceQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', true, Translation :: get(get_class($this) .'Properties'));
		$this->add_options();
		$this->addElement('category');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', true, Translation :: get(get_class($this) .'Properties'));
		$this->add_options();
		$this->addElement('category');
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
					$defaults['weight'][$index] = $option->get_weight();
					if($object->get_answer_type() == 'checkbox')
					{
						$defaults['correct'][$index] = $option->is_correct();
					}
					elseif($option->is_correct())
					{
						$defaults['correct'] = $index;
					}
				}
			}
		}
		//print_r($defaults);
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$object = new MultipleChoiceQuestion();
		$this->set_learning_object($object);
		$this->add_options_to_object();
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$this->add_options_to_object();
		return parent :: update_learning_object();
	}
	private function add_options_to_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$options = array();
		foreach($values['option'] as $option_id => $value)
		{
			$weight = $values['weight'][$option_id];
			if($_SESSION['mc_answer_type'] == 'radio')
			{
				$correct = $values['correct'] == $option_id;
			}
			else
			{
				$correct = $values['correct'][$option_id];
			}
			$options[] = new MultipleChoiceQuestionOption($value,$correct,$weight);
		}
		$object->set_answer_type($_SESSION['mc_answer_type']);
		$object->set_options($options);
	}
	function validate()
	{
		if(isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
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
			unset($_SESSION['mc_answer_type']);
		}
		if(!isset($_SESSION['mc_number_of_options']))
		{
			$_SESSION['mc_number_of_options'] = 3;
		}
		if(!isset($_SESSION['mc_skip_options']))
		{
			$_SESSION['mc_skip_options'] = array();
		}
		if(!isset($_SESSION['mc_answer_type']))
		{
			$_SESSION['mc_answer_type'] = 'radio';
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
		if(isset($_POST['change_answer_type']))
		{
			$_SESSION['mc_answer_type'] = $_SESSION['mc_answer_type'] == 'radio' ? 'checkbox' : 'radio';
		}
		$object = $this->get_learning_object();
		if(!$this->isSubmitted() && !is_null($object))
		{
			$_SESSION['mc_number_of_options'] = $object->get_number_of_options();
			$_SESSION['mc_answer_type'] = $object->get_answer_type();
		}
		$number_of_options = intval($_SESSION['mc_number_of_options']);
		//Todo: Style this element
		$this->addElement('submit','change_answer_type','radio <-> checkbox');
		$show_label = true;
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			if(!in_array($option_number,$_SESSION['mc_skip_options']))
			{
				$group = array();
				if($_SESSION['mc_answer_type'] == 'checkbox')
				{
					$group[] = $this->createElement('checkbox','correct['.$option_number.']');
				}
				else
				{
					$group[] = $this->createElement('radio','correct','','',$option_number);
				}
				$group[] = $this->createElement('text','option['.$option_number.']', '','size="40"');
				$group[] = $this->createElement('text','weight['.$option_number.']','','size="2"  class="input_numeric"');
				if($number_of_options - count($_SESSION['mc_skip_options']) > 2)
				{
					$group[] = $this->createElement('image','remove['.$option_number.']',Theme :: get_common_img_path().'action_list_remove.png');
				}
				$label = $show_label ? Translation :: get('Answers') : '';
				$show_label = false;
				$this->addGroup($group,'options_group_'.$option_number,$label,'',false);
				$this->addGroupRule('options_group_'.$option_number,
					array(
						'option['.$option_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'),'required'
								)
							),
						'weight['.$option_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'), 'required'
								),
								array(
									Translation :: get('ValueShouldBeNumeric'),'numeric'
								)
							)
					)
				);
			}
		}
		$this->addFormRule(array('MultipleChoiceQuestionForm','validate_selected_answers'));
		//Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
		$this->addElement('image','add[]',Theme :: get_common_img_path().'action_list_add.png');
	}
	function validate_selected_answers($fields)
	{
		if(!isset($fields['correct']))
		{
			$message = $_SESSION['mc_answer_type'] == 'checkbox' ? Translation :: get('SelectAtLeastOneCorrectAnswer') : Translation :: get('SelectACorrectAnswer');
			 return array('change_answer_type' => $message);
		}
		return true;
	}
}
?>