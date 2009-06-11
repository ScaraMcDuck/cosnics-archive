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
		$this->build_options_and_matches();
	}
	
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->build_options_and_matches();
	}
	/**
	 * Adds the options and matches to the form
	 */
	private function build_options_and_matches()
	{
		$this->update_number_of_options_and_matches();
		$this->add_options();
		$this->add_matches();
	}
	
	function setDefaults($defaults = array ())
	{
		//if(!$this->isSubmitted())
		//{
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
			$number_of_options = intval($_SESSION['mq_number_of_options']);
		
			for($option_number = 0; $option_number <$number_of_options ; $option_number++)
			{
				$defaults['option_weight'][$option_number] = 1;
			}

		parent :: setDefaults($defaults);
	}
	
	function setCsvValues($valuearray)
	{	
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
			$options[] = new MatchingQuestionOption($value, 
							$matches_indexes[$values['matches_to'][$option_id]],
							$values['option_weight'][$option_id],
							$values['comment'][$option_id]);
		}
		
		foreach($values['match'] as $match)
		{
			$matches[] = $match;
		}
		$object->set_options($options);
		$object->set_matches($matches);
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
			$_SESSION['mq_number_of_matches'] = 3;
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
		$matches = array();
		$match_label = 'A';
		
		for($match_number = 0; $match_number<$_SESSION['mq_number_of_matches']; $match_number++)
		{
			if(!in_array($match_number, $_SESSION['mq_skip_matches']))
			{
				$matches[$match_number] = $match_label++;
			}
		}
		
		$this->addElement('category', Translation :: get('Options'));
		
		$buttons = array();
        $buttons[] = $this->createElement('style_button', 'add_option[]', Translation :: get('AddMultipleChoiceOption'), array('class' => 'normal add'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);
		
		$renderer = $this->defaultRenderer();
		
		$table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th style="width: 50px;">' . Translation :: get('Matches') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Answer') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th style="width: 50px;">' . Translation :: get('Weight') . '</th>';
        $table_header[] = '<th style="width: 25px;"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
		
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['show_toolbar'] = false;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
		
		for($option_number = 0; $option_number <$number_of_options ; $option_number++)
		{
			$group = array();
			if(!in_array($option_number,$_SESSION['mq_skip_options']))
			{
				$group[] = $this->createElement('select','matches_to['.$option_number.']',Translation :: get('Matches'),$matches);
				$group[] = $this->create_html_editor('option[' . $option_number . ']', Translation :: get('Answer'), $html_editor_options);
				$group[] = $this->create_html_editor('comment[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
				$group[] = $this->createElement('text','option_weight['.$option_number.']', Translation :: get('Weight'), 'size="2"  class="input_numeric"');
				
				if($number_of_options - count($_SESSION['mc_skip_options']) > 2)
				{
					$group[] = $this->createElement('image','remove_option['.$option_number.']',Theme :: get_common_image_path().'action_list_remove.png');
				}

				$this->addGroup($group, 'option_' . $option_number, null, '', false);
			
				$renderer->setElementTemplate('<tr>{element}</tr>', 'option_' . $option_number);
   				$renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);

				$this->addGroupRule('option_'.$option_number,
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
				);
			}
		}
		$table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));
        
        $this->addGroup($buttons, 'question_buttons', null, '', false);
      	
      	$renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
      	
		$this->addElement('category');
	}
	
	/**
	 * Adds the form-fields to the form to provide the possible matches for this
	 * matching question
	 */
	private function add_matches()
	{
		$number_of_matches = intval($_SESSION['mq_number_of_matches']);
		$this->addElement('category', Translation :: get('Matches'));
		
		$buttons = array();
        $buttons[] = $this->createElement('style_button', 'add_match[]', Translation :: get('AddMatch'), array('class' => 'normal add'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);
		
		$renderer = $this->defaultRenderer();
		
		$table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th style="width: 50px;">' . Translation :: get('Match') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Answer') . '</th>';
        if($number_of_matches - count($_SESSION['mq_skip_matches']) > 2)
		{
       		$table_header[] = '<th style="width: 25px;"></th>';
		}
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
		
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['show_toolbar'] = false;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
		
		$label = 'A';
		for($match_number = 0; $match_number <$number_of_matches ; $match_number++)
		{
			$group = array();
			
			if(!in_array($match_number,$_SESSION['mq_skip_matches']))
			{
				$defaults['match_label'][$match_number] = $label++;
				$element = $this->createElement('text','match_label['.$match_number.']', Translation :: get('Match'), 'style="width: 90%;" ');
				$element->freeze();
				$group[] = $element;
				$group[] = $this->create_html_editor('match[' . $match_number . ']', Translation :: get('Match'), $html_editor_options);
				
				if($number_of_matches - count($_SESSION['mq_skip_matches']) > 2)
				{
					$group[] = $this->createElement('image','remove_match['.$match_number.']',Theme :: get_common_image_path().'action_list_remove.png');
				}
				
				$this->addGroup($group, 'match_' . $match_number, null, '', false);
			
				$renderer->setElementTemplate('<tr>{element}</tr>', 'match_' . $match_number);
   				$renderer->setGroupElementTemplate('<td>{element}</td>', 'match_' . $match_number);
				
				
				$this->addGroupRule('match_'.$match_number,
					array(
						'match['.$match_number.']' =>
							array(
								array(
									Translation :: get('ThisFieldIsRequired'),'required'
								)
							),
					)
				);
			}
			
			$this->setConstants($defaults);
		}
		
		$table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));
		
        $this->addGroup($buttons, 'question_buttons', null, '', false);
      	
      	$renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
        
		$this->addElement('category');
	}
}
?>
