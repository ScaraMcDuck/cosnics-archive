<?php

class QuestionDisplay 
{
	private $clo_question;
	
	function QuestionDisplay($clo_question)
	{
		$this->clo_question = $clo_question;
	}
	
	function get_clo_question()
	{
		return $this->clo_question;
	}
	
	function add_to($formvalidator) {
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$formvalidator->addElement('html', $this->display_header($question));
	}

	function display_header()
	{
		$clo_question = $this->get_clo_question();
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="title">';
		$html[] = $learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $learning_object->get_description();
		
		return implode("\n", $html);
	}
	
	function display_footer()
	{
		$html[] = '<br/></div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}

	static function factory($clo_question) {
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$type = $question->get_type();
			
		switch($type)
		{
		case 'open_question':
			$question_display = new OpenQuestionDisplay($clo_question);
			break;
		case 'fill_in_blanks_question':
			$question_display = new FillInBlanksQuestionDisplay($clo_question);
			break;
		case 'matching_question':
			$question_display = new MatchingQuestionDisplay($clo_question);
			break;
		case 'multiple_choice_question':
			$question_display = new MultipleChoiceQuestionDisplay($clo_question);
			break;
		case 'rating_question':
			$question_display = new ScoreQuestionDisplay($clo_question);
			break;
		case 'hotspot_question':
			$question_display = new HotSpotQuestionDisplay($clo_question);
			break;
		/*case Question :: TYPE_OPEN:
			$question_display = new OpenQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_OPEN_WITH_DOCUMENT:
			$question_display = new OpenQuestionWithDocumentDisplay($clo_question);
			break;
		case Question :: TYPE_DOCUMENT:
			$question_display = new DocumentQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_FILL_IN_BLANKS:
			$question_display = new FillInBlanksQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_MATCHING:
			$question_display = new MatchingQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_MULTIPLE_ANSWER:
			$question_display = new MultipleAnswerQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_MULTIPLE_CHOICE:
			$question_display = new MultipleChoiceQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_PERCENTAGE:
			$question_display = new PercentageQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_SCORE:
			$question_display = new ScoreQuestionDisplay($clo_question);
			break;
		case Question :: TYPE_YES_NO:
			$question_display = new YesNoQuestionDisplay($clo_question);
			break;*/
		default:
			$question_display = null;
		}
		return $question_display;
	}
}
?>