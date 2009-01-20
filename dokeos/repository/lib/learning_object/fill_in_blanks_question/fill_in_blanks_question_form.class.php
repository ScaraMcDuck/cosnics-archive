<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/fill_in_blanks_answer.class.php';
class FillInBlanksQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->add_options();
		$this->addElement('category');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
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
				$options = $object->get_answers();
				foreach($options as $index => $option)
				{
					$defaults['option'][$index] = $option->get_value();
					$defaults['weight'][$index] = $option->get_weight();
				}
			}
		}
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$object = new FillInBlanksQuestion();
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
			$options[] = new FillInBlanksQuestionAnswer($value, $weight);
		}
		$object->set_answers($options);
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
		$object = $this->get_learning_object();
		if(!$this->isSubmitted() && !is_null($object))
		{
			$_SESSION['mc_number_of_options'] = $object->get_number_of_options();
			$_SESSION['mc_answer_type'] = $object->get_answer_type();
		}
		$number_of_options = intval($_SESSION['mc_number_of_options']);
		$show_label = true;
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			if(!in_array($option_number,$_SESSION['mc_skip_options']))
			{
				$group = array();
				$group[] = $this->createElement('text','option['.$option_number.']', '','size="40"');
				$group[] = $this->createElement('text','weight['.$option_number.']','','size="2"  class="input_numeric"');
				if($number_of_options - count($_SESSION['mc_skip_options']) > 2)
				{
					$group[] = $this->createElement('image','remove['.$option_number.']',Theme :: get_common_image_path().'action_list_remove.png');
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
		//Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
		$this->addElement('image','add[]',Theme :: get_common_image_path().'action_list_add.png');
	}
}
?>
