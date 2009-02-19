<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/matching_question.class.php';
class MatchingQuestionForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->build_options_and_matches();
		$this->addElement('category');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->build_options_and_matches();
		$this->addElement('category');
	}
	/**
	 * Adds the options and matches to the form
	 */
	private function build_options_and_matches()
	{
		$this->update_number_of_options_and_matches();
		//$this->addElement('html','<div class="row"><div class="label">'.Translation :: get('Answers').'</div><div class="formw"><table style="width: 100%"><tr><td style="text-align:left; width: 50%; vertical-align: top;">');
		$this->add_options();
		//$this->addElement('html','</td><td style="text-align:left; width: 50%; vertical-align: top;">');
		$this->add_matches();
		//$this->addElement('html','</td></tr></table></div></div>');
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
					$defaults['option_weight'][$index] = $option->get_weight();
					$defaults['matches_to'][$index] = $option->get_match();
					$defaults['comment'][$index] = $option->get_comment();
				}
				$matches = $object->get_matches();
				foreach($matches as $index => $match)
				{
					$defaults['match'][$index] = $match;
				}
			}
		}
		parent :: setDefaults($defaults);
	}
	function setCsvValues($valuearray)
	{	
		//Required 
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];
		parent :: setValues($defaults);		
	}	
	function create_learning_object()
	{
		$object = new MatchingQuestion();
		$this->set_learning_object($object);
		$this->add_answer();
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$this->add_answer();
		return parent :: update_learning_object();
	}
	/**
	 * Adds the answer to the current learning object.
	 * This function adds the list of possible options and matches and the
	 * relation between the options and the matches to the question.
	 */
	private function add_answer()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$options = array();
		$matches = array();
		//Get an array with a mapping from the match-id to its index in the $values['match'] array
		$matches_indexes = array_flip(array_keys($values['match']));
		foreach($values['option'] as $option_id => $value)
		{
			//Create the option with it corresponding match
			$options[] = new MatchingQuestionOption($value,$matches_indexes[$values['matches_to'][$option_id]],$values['option_weight'][$option_id],$values['comment'][$option_id]);
		}
		foreach($values['match'] as $match_id => $match)
		{
			$matches[] = $match;
		}
		$object->set_options($options);
		$object->set_matches($matches);
	}
	function validate()
	{
		//Don't validate the form if the user just wants to change the number of options or matches
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
			unset($_SESSION['mq_number_of_matches']);
			unset($_SESSION['mq_skip_matches']);
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
		$object = $this->get_learning_object();
		if(!$this->isSubmitted() && !is_null($object))
		{
			$_SESSION['mq_number_of_options'] = $object->get_number_of_options();
			$_SESSION['mq_number_of_matches'] = $object->get_number_of_matches();
		}
	}
	/**
	 * Adds the form-fields to the form to provide the possible options for this
	 * multiple choice question
	 * @todo Add rules to require options and matches
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
		
		$count = 1;
		
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			if(!in_array($option_number,$_SESSION['mq_skip_options']))
			{
				/*$group = array();
				$group[] = $this->createElement('text','option['.$option_number.']', '', true,'size="40"');
				$group[] = $this->createElement('text','option_weight['.$option_number.']','','size="2" class="input_numeric"');
				$group[] = $this->createElement('select','matches_to['.$option_number.']','',$matches);
				$group[] = $this->createElement('text','comment['.$option_number.']', '', true,'size="40"');
				if($number_of_options - count($_SESSION['mq_skip_options']) > 2)
				{
					$group[] = $this->createElement('image','remove_option['.$option_number.']',Theme :: get_common_image_path().'action_list_remove.png');
				}*/
				
				$this->addElement('category', Translation :: get('Answer') . ' ' . ($count));

				$this->add_html_editor('option['.$option_number.']', Translation :: get('Answer'), true);
				$this->add_html_editor('comment['.$option_number.']', Translation :: get('Comment'), false);
				$this->addElement('select','matches_to['.$option_number.']','',$matches);
				$this->addElement('text','option_weight['.$option_number.']', Translation :: get('Weight'), 'size="2"  class="input_numeric"');
				$this->addRule('option_weight['.$option_number.']', Translation :: get('ThisFieldIsRequired'), 'required');
				$this->addRule('option_weight['.$option_number.']', Translation :: get('ValueShouldBeNumeric'), 'numeric');
				
				if($number_of_options - count($_SESSION['mc_skip_options']) > 2)
				{
					$this->addElement('image','remove['.$option_number.']',Theme :: get_common_image_path().'action_list_remove.png');
				}
				$this->addElement('category');
				$count++;
				
				/*$this->addGroup($group,'options_group_'.$option_number,$label++,'',false);
				$this->addGroupRule('options_group_'.$option_number,
					array(
						'option['.$option_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'),'required'
								)
							),
						'option_weight['.$option_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'), 'required'
								),
								array(
									Translation :: get('ValueShouldBeNumeric'),'numeric'
								)
							)
					)
				);*/
			}
		}
		//Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
		$this->addElement('image','add_option[]',Theme :: get_common_image_path().'action_list_add.png');
	}
	/**
	 * Adds the form-fields to the form to provide the possible matches for this
	 * matching question
	 */
	private function add_matches()
	{
		$number_of_matches = intval($_SESSION['mq_number_of_matches']);
		$label = 'A'; $this->addElement('category', Translation :: get('Matches'));
		for($match_number = 0; $match_number <$number_of_matches ; $match_number++)
		{
			if(!in_array($match_number,$_SESSION['mq_skip_matches']))
			{
				
				$this->add_html_editor('match['.$match_number.']', Translation :: get('Match') . ' ' . $label++, true);
				
				/*$group = array();
				$group[] = $this->createElement('text','match['.$match_number.']', '', true,'size="40"');
				if($number_of_matches - count($_SESSION['mq_skip_matches']) > 2)
				{
					$group[] = $this->createElement('image','remove_match['.$match_number.']',Theme :: get_common_image_path().'action_list_remove.png');
				}
				$this->addGroup($group,'matches_group_'.$match_number,$label++,'',false);
				$this->addGroupRule('matches_group_'.$match_number,
					array(
						'match['.$match_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'),'required'
								)
							),
					)
				);*/
			}
		}
		
		$this->addElement('category');
		
		//Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
		$this->addElement('image','add_match[]',Theme :: get_common_image_path().'action_list_add.png');
	}
}
?>
