<?php

require_once dirname(__FILE__).'/question_result_types/document_question.class.php';
require_once dirname(__FILE__).'/question_result_types/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/question_result_types/matching_question.class.php';
require_once dirname(__FILE__).'/question_result_types/multiple_answer_question.class.php';
require_once dirname(__FILE__).'/question_result_types/multiple_choice_question.class.php';
require_once dirname(__FILE__).'/question_result_types/open_question_with_document.class.php';
require_once dirname(__FILE__).'/question_result_types/open_question.class.php';
require_once dirname(__FILE__).'/question_result_types/percentage_question.class.php';
require_once dirname(__FILE__).'/question_result_types/score_question.class.php';
require_once dirname(__FILE__).'/question_result_types/yes_no_question.class.php';

abstract class QuestionResult
{
	private $user_question;
	private $question;
	private $formvalidator;
	
	private $clo_question;
	private $user_answers;
	private $clo_answers;
	
	private $edit_rights;
	
	function QuestionResult($formvalidator, $user_question, $question, $edit_rights = 0) 
	{
		$this->question = $question;
		$this->user_question = $user_question;
		$this->formvalidator = $formvalidator;
		$this->edit_rights = $edit_rights;
		$this->init();
	}
	
	function init()
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->user_question->get_question_id());
		$this->clo_question = $dm->retrieve_complex_learning_object_items($condition)->next_result();
		$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $this->user_question->get_id());
		$answers = WeblcmsDataManager :: get_instance()->retrieve_user_answers($condition);
		
		while ($user_answer = $answers->next_result())
		{
			$this->user_answers[] = $user_answer;
		}
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->question->get_id());
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		while ($clo_answer = $clo_answers->next_result())
		{
			$this->clo_answers[] = $clo_answer;
		}
	}
	
	function get_user_question()
	{
		return $this->user_question;
	}
	
	function get_question()
	{
		return $this->question;
	}
	
	function get_edit_rights()
	{
		return $this->edit_rights;
	}
	
	function get_clo_question()
	{
		return $this->clo_question;
	}
	
	function get_user_answers()
	{
		return $this->user_answers;
	}
	
	function get_clo_answers()
	{
		return $this->clo_answers;
	}
	
	function add_feedback_controls()
	{
		$this->formvalidator->addElement('html', '<br/>'.Translation :: get("Add feedback").':<br/>');
		//$this->formvalidator->addElement('html_editor', 'new'.$this->user_question->get_id());
		
		$lo_feedback_rs = RepositoryDataManager :: get_instance()->retrieve_learning_objects('feedback');
		$feedback_objects[] = Translation :: get('No feedback');
		while ($lo_feedback = $lo_feedback_rs->next_result())
		{
			$feedback_objects[] = $lo_feedback->get_id();
		}
		$this->formvalidator->addElement('select', 'ex'.$this->user_question->get_id(), Translation :: get('Select a feedback object:'), $feedback_objects);
	}
	
	function add_score_controls($max_score)
	{
		for ($i = 0; $i <= $max_score; $i++)
		{
			$values[] = $i;
		}
		$this->formvalidator->addElement('select', 'score'.$this->user_question->get_id(), Translation :: get('Change score:'), $values);
	}
	
	function display_question_header()
	{
		$learning_object = $this->question;
		$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path(). 'learning_object/' .$learning_object->get_icon_name().'.png);">';
		$html[] = '<div class="title">';
		$html[] = Translation :: get('Question').' '.$learning_object->get_description();
		$html[] = '</div>';
		
		$this->formvalidator->addElement('html', implode("\n", $html));
	}
	
	function display_score($score_line)
	{
		$html[] = '<div class="description">';
		$html[] = $score_line.'<br/><br/>';
		$html[] = '</div>';
		$this->formvalidator->addElement('html', implode("\n", $html));
	}
	
	function display_feedback()
	{
		$this->formvalidator->addElement('html', '<br/><br/><div class="title">Feedback:</div>');
		$feedback_id = $this->user_question->get_feedback();
		//echo $feedback_id;
		if ($feedback_id != null)
		{
			$feedback_lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($feedback_id, 'feedback');
			$this->formvalidator->addElement('html', '<div class="description">'.$feedback_lo->get_description().$this->render_attachments($feedback_lo).'</div>');
		}
		else
			$this->formvalidator->addElement('html', '<div class="description">No feedback yet</div>');
	}
	
	function render_attachments($object)
	{
		if ($object->supports_attachments())
		{
			$attachments = $object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				//$html[] = '<b>Attachments:</b><br/><br/>';
				$html[] = '<ul class="attachments_list">';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = LearningObjectDisplay :: factory($attachment);
					//$html[] = '<li><img src="'.Theme :: get_common_img_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
					$html[] = '<li><img src="'.Theme :: get_common_img_path().'/action_attachment.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}
	
	function display_answers($answer_lines = null, $numbered = true)
	{
		if ($answer_lines == null) 
		{
			return;
		}
		else
		{	
			if (sizeof($answer_lines) == 1)
			{
				$this->formvalidator->addElement('html', '<div class="title">'.Translation :: get('Answers').': </div>'.$answer_lines[0]);
			}
			else 
			{
				if ($numbered) 
					$list_items = '<ol>';
				else 
					$list_items = '<ul>';
					
				for ($i = 0; $i < sizeof($answer_lines); $i++)
				{
					$list_items .= '<li>'.$answer_lines[$i].'</li>';
				}
				
				if ($numbered) 
					$list_items .= '</ol>';
				else 
					$list_items .= '</ul>';
				
				$this->formvalidator->addElement('html', '<div class="title">'.Translation :: get('Answers').': </div><div class="description">'.$list_items.'</div>');
			}
		}
	}
	
	function display_footer()
	{
		$this->formvalidator->addElement('html', '</div>');
	}
	
	static function create_question_result($formvalidator, $user_question, $edit_rights)
	{
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_question->get_question_id());
		switch ($question->get_question_type())
		{
			case Question :: TYPE_DOCUMENT:
				return new DocumentQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_FILL_IN_BLANKS:
				return new FillInBlanksQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_MATCHING:
				return new MatchingQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_MULTIPLE_ANSWER:
				return new MultipleAnswerQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_MULTIPLE_CHOICE:
				return new MultipleChoiceQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_OPEN:
				return new OpenQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				return new OpenQuestionWithDocumentResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_PERCENTAGE:
				return new PercentageQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_SCORE:
				return new ScoreQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			case Question :: TYPE_YES_NO:
				return new YesNoQuestionResult($formvalidator, $user_question, $question, $edit_rights);
			default:
				return null;
		}
	}
	
	abstract function display_exercise();
	
	abstract function display_survey();
	
	abstract function display_assignment();
}
?>