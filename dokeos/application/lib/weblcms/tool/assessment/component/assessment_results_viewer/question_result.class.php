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
	private $results;
	private $question;
	private $formvalidator;
	
	private $clo_question;
	private $user_answers;
	private $clo_answers;
	
	private $edit_rights;
	
	private $question_nr;
	
	function QuestionResult($formvalidator, $q_results, $question, $edit_rights = 0, $question_nr) 
	{
		$this->results = $q_results;
		$this->question = $question;
		$this->formvalidator = $formvalidator;
		$this->edit_rights = $edit_rights;
		$this->question_nr = $question_nr;
		$this->init();
	}
	
	function init()
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->question->get_id());
		$this->clo_question = $dm->retrieve_complex_learning_object_items($condition)->next_result();
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
	
	function get_results()
	{
		return $this->results;
	}
	
	function add_feedback_controls()
	{
		if ($this->results != null)
		{
			$this->formvalidator->addElement('html', '<br/>'.Translation :: get("AddFeedback").':<br/>');
			
			$result = $this->results[0];
			$this->formvalidator->addElement('hidden', 'ex_'.$this->get_question()->get_id(), '');
			$this->formvalidator->addElement('text', 'ex'.$this->get_question()->get_id().'_name', Translation :: get('SelectedFeedback'), array('DISABLED=DISABLED'));
			$buttons[] = $this->formvalidator->createElement('style_submit_button', 'feedback_'.$this->get_question()->get_id(), Translation :: get('Select'), array('class' => 'positive'));

			$this->formvalidator->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		}
	}
	
	function add_score_controls($max_score)
	{
		for ($i = 0; $i <= $max_score; $i++)
		{
			$values[] = $i;
		}
		if ($this->results != null)
		{
			$result = $this->results[0];
			$this->formvalidator->addElement('select', 'score'.$this->get_question()->get_id(), Translation :: get('ChangeScore'), $values);
		}
	}
	
	function display_question_header()
	{
		$learning_object = $this->question;
		//$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$learning_object->get_icon_name().'.png);">';
		//$html[] = '<div class="title">';
		$html[] = '<div class="question">';
		$html[] = '<div class="title" style="padding: 5px 5px 5px 35px; border:1px solid grey; background: #e6e6e6 url('. Theme :: get_common_image_path(). 'learning_object/' .$learning_object->get_icon_name().'.png) no-repeat; background-position: 5px 2px; height: 16px;">';
		$html[] = Translation :: get('Question').' '. $this->question_nr . ': ' .$learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description" style="padding-left: 35px;">';
		$description = $learning_object->get_description();
		
		if($description != '<p>&#160;</p>')
			$html[] = '<div style="font-style: italic; ">' . $description . '</div>';
		else
			$html[] = '<br />';
		
		$html[] = '<table border="0" style="width: 100%">';
		$html[] = '<tr>';
		$html[] = '<td style="width: 33%">' . Translation :: get('YourAnswer') . '</td>';
		$html[] = '<td style="width: 33%">' . Translation :: get('CorrectAnswer') . '</td>';
		$html[] = '<td style="width: 33%">' . Translation :: get('Feedback') . '</td>';
		$html[] = '</tr><tr>';
		
		$this->formvalidator->addElement('html', implode("\n", $html));
	}
	
	function display_score($score_line)
	{
		//$html[] = '<div class="description">';
		$html[] = '</tr></table><br /><b>' . $score_line. '</b><br/>';
		//$html[] = '</div>';
		$this->formvalidator->addElement('html', implode("\n", $html));
	}
	
	function display_feedback()
	{
		if ($this->results != null)
		{
			$result = $this->results[0];
			$feedback_id = $result->get_feedback();
			if ($feedback_id != null && $feedback_id != 0)
			{
				$feedback_lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($feedback_id, 'feedback');
				$this->formvalidator->addElement('html', '<td>'.$feedback_lo->get_title().'<br/><br/>'.$feedback_lo->get_description().$this->render_attachments($feedback_lo).'</td>');
			}
			else
				$this->formvalidator->addElement('html', '<td>' . Translation :: get('NoFeedback') . '</td>');
		}
		else
		{
			$this->formvalidator->addElement('html', '<td>' . Translation :: get('QuestionNotAnswered') . '</td>');
		}
	}
	
	function render_attachments($object)
	{
		if ($object->supports_attachments())
		{
			$attachments = $object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				$html[] = '<ul class="attachments_list">';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = LearningObjectDisplay :: factory($attachment);
					$html[] = '<li><img src="'.Theme :: get_common_image_path().'/action_attachment.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}
	
	function display_answers($answer_lines = null, $correct_answer_lines, $numbered = true, $use_list = true)
	{ 
		if ($answer_lines == null) 
		{
			$this->formvalidator->addElement('html', '<td><br />'. Translation :: get('NoAnswer') . '<br /><br /></td><td>&nbsp;</td>');
		}
		else
		{	
			if (sizeof($answer_lines) == 1 && sizeof($correct_answer_lines) == 1)
			{
				if($answer_lines[0] == '<p>&#160;</p>')
					$answer_lines[0] = Translation :: get('NoAnswer');
					
				$this->formvalidator->addElement('html', '<td><br />' . $answer_lines[0] . '<br /><br /></td>');
				$this->formvalidator->addElement('html', '<td>' . $correct_answer_lines[0] . '</td>');
			}
			else 
			{
				if($use_list)
				{
					if ($numbered) 
					{
						$list_items = '<ol>';
						$list_correct_items = '<ol >';
					}
					else
					{ 
						$list_items = '<ul>';
						$list_correct_items = '<ul>';
					}
				}
				else
				{
					$list_items = '<ul style="list-style-type: none; padding: 0px;">';
					$list_correct_items = '<ul style="list-style-type: none; padding: 0px;">';
				}
					
				for ($i = 0; $i < sizeof($answer_lines); $i++)
				{
					$list_items .= '<li>'.$answer_lines[$i].'</li>';
				}
				
				
				for ($i = 0; $i < sizeof($correct_answer_lines); $i++)
				{
					$list_correct_items .= '<li>'.$correct_answer_lines[$i].'</li>';
				}
				
				if ($numbered || !$use_list) 
				{
					$list_items .= '</ol>';
					$list_correct_items .= '</ol>';
				}
				else
				{ 
					$list_items .= '</ul>';
					$list_correct_items .= '</ul>';
				}
				
				$this->formvalidator->addElement('html', '<td>'.$list_items.'</td>');
				$this->formvalidator->addElement('html', '<td>'. $list_correct_items . '</td>');
			}
		}

	}
	
	function display_footer()
	{
		$this->formvalidator->addElement('html', '<br /></div></div>');
	}
	
	static function create_question_result($formvalidator, $question, $q_results, $edit_rights, $question_nr)
	{

		switch ($question->get_type())
		{
			case 'open_question':
				return new OpenQuestionResult($formvalidator, $q_results, $question, $edit_rights, $question_nr);
			case 'multiple_choice_question':
				return new MultipleChoiceQuestionResult($formvalidator, $q_results, $question, $edit_rights, $question_nr);
			case 'matching_question':
				return new MatchingQuestionResult($formvalidator, $q_results, $question, $edit_rights, $question_nr);
			case 'fill_in_blanks_question':
				return new FillInBlanksQuestionResult($formvalidator, $q_results, $question, $edit_rights, $question_nr);
			case 'rating_question':
				return new ScoreQuestionResult($formvalidator, $q_results, $question, $edit_rights, $question_nr);
			case 'hotspot_question':
				return null;
			default:
				return null;
		}
	}
	
	abstract function display_exercise();
	
	abstract function display_survey();
	
	abstract function display_assignment();
}
?>