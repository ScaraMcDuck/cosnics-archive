<?php

require_once dirname(__FILE__).'/question_types/document_import.class.php';
require_once dirname(__FILE__).'/question_types/fill_in_blanks_import.class.php';
require_once dirname(__FILE__).'/question_types/matching_import.class.php';
require_once dirname(__FILE__).'/question_types/multiple_answer_import.class.php';
require_once dirname(__FILE__).'/question_types/multiple_choice_import.class.php';
require_once dirname(__FILE__).'/question_types/open_question_import.class.php';
require_once dirname(__FILE__).'/question_types/open_question_with_document_import.class.php';
require_once dirname(__FILE__).'/question_types/percentage_import.class.php';
require_once dirname(__FILE__).'/question_types/score_import.class.php';

class QuestionQtiImport extends QtiImport
{
	
	function import_learning_object()
	{
		$importer = $this->factory_qti_question($this->get_learning_object_file(), $this->get_user(), $this->get_category());
		return $importer->import_learning_object();
	}
	
	function factory_qti_question($lo_file, $user, $category)
	{
		$data = $this->get_file_content_array();
		$itembody = $data['itemBody'];
		foreach ($itembody as $key => $itemdata)
		{
			$tag_type = substr($key, (strlen($key) - strlen('Interaction')), strlen($key));
			if ($tag_type == 'Interaction')
			{
				$tag = $key;
				//options needed to differentiate between question types with the same tag
				$num_choices = $itemdata['maxChoices'];
				$ubound = $itemdata['upperBound'];
				break;
			}
			if ($key == 'blockquote')
			{
				if ($itemdata['p']['textEntryInteraction'] != null || $itemdata['textEntryInteraction'] != null)
				{
					$tag = 'textEntryInteraction';
					break;
				}
			}
		}
		switch ($tag)
		{
			case 'extendedTextInteraction':
				return new OpenQuestionQtiImport($lo_file, $user, $category);
			case 'uploadInteraction':
				return new DocumentQuestionQtiImport($lo_file, $user, $category);
			case 'choiceInteraction':
				if ($num_choices == 1)
					return new MultipleChoiceQuestionQtiImport($lo_file, $user, $category);
				else
					return new MultipleAnswerQuestionQtiImport($lo_file, $user, $category);
			case 'sliderInteraction':
				return new ScoreQuestionQtiImport($lo_file, $user, $category);
			case 'textEntryInteraction':
				return new FillInBlanksQuestionQtiImport($lo_file, $user, $category);
			case 'matchInteraction': 
				return new MatchingQuestionQtiImport($lo_file, $user, $category);
			default:
				return null;
		}
	}
	
	function create_question($question)
	{
		/*$question = new Question();
		$question->set_title($title);
		$question->set_description($description);
		$question->set_question_type($question_type);
		$question->set_owner_id($this->get_user()->get_id());
	
		$question->create();
		
		return $question;*/
		$question->set_owner_id($this->get_user()->get_id());
		return $question->create();
	}
	
	/*function create_answer($title, $description = '')
	{
		$answer = new Answer();
		$answer->set_title($title);
		$answer->set_description($description);
		$answer->set_owner_id($this->get_user()->get_id());
		
		$answer->create();
		
		return $answer;
	}*/
	
	/*function create_complex_answer($question, $answer, $score, $display_order = '1')
	{
		$clo_answer = new ComplexAnswer();
		$clo_answer->set_ref($answer->get_id());
		$clo_answer->set_parent($question->get_id());
		$clo_answer->set_score($score);
		$clo_answer->set_additional_property(ComplexAnswer :: PROPERTY_DISPLAY_ORDER, $display_order);
		$clo_answer->set_user_id($this->get_user()->get_id());
		
		$clo_answer->create();
		
		return $clo_answer;
	}*/
}
?>