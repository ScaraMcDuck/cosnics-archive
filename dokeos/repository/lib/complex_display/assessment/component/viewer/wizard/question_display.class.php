<?php

class QuestionDisplay 
{
	private $clo_question;
	private $question_nr;
	
	function QuestionDisplay($clo_question, $question_nr)
	{
		$this->clo_question = $clo_question;
		$this->question_nr = $question_nr;
	}
	
	function get_clo_question()
	{
		return $this->clo_question;
	}
	
	function add_to($formvalidator) {
		$formvalidator->addElement('html', $this->display_header());
	}

	function display_header()
	{
		$clo_question = $this->get_clo_question();
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		
		$html[] = '<div class="question">';
		$html[] = '<div class="title" style="padding: 5px 5px 5px 35px; border:1px solid grey; background: #e6e6e6 url('. Theme :: get_common_image_path(). 'learning_object/' .$learning_object->get_icon_name().'.png) no-repeat; background-position: 5px 2px; height: 16px;">';
		$html[] = Translation :: get('Question') . ' ' . $this->question_nr . ': ' . $learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description" style="padding-left: 35px;">';
		$description = $learning_object->get_description();

		if($description != '<p>&#160;</p>' && count($description) > 0 )
			$html[] = '<div style="font-style: italic; ">' . $description . '</div>';
		else
			$html[] = '<br />';
			
		/*$html[] = '</div>';
		$html[] = '<div class="answers">';*/
		
		return implode("\n", $html);
	}
	
	function display_footer()
	{
		$html[] = '<br/></div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}

	static function factory($clo_question, $question_nr) {
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$type = $question->get_type();
		
		require_once dirname(__FILE__) . '/question_display/' . $type . '.class.php';
		
		switch($type)
		{
		case 'open_question':
			$question_display = new OpenQuestionDisplay($clo_question, $question_nr);
			break;
		case 'fill_in_blanks_question':
			$question_display = new FillInBlanksQuestionDisplay($clo_question, $question_nr);
			break;
		case 'matching_question':
			$question_display = new MatchingQuestionDisplay($clo_question, $question_nr);
			break;
		case 'multiple_choice_question':
			$question_display = new MultipleChoiceQuestionDisplay($clo_question, $question_nr);
			break;
		case 'rating_question':
			$question_display = new RatingQuestionDisplay($clo_question, $question_nr);
			break;
		case 'hotspot_question':
			$question_display = new HotSpotQuestionDisplay($clo_question, $question_nr);
			break;
		default:
			$question_display = null;
		}
		return $question_display;
	}
}
?>