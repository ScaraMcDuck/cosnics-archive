<?php

require_once dirname(__FILE__).'/question_result_types/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/question_result_types/matching_question.class.php';
require_once dirname(__FILE__).'/question_result_types/multiple_choice_question.class.php';
require_once dirname(__FILE__).'/question_result_types/open_question.class.php';
require_once dirname(__FILE__).'/question_result_types/score_question.class.php';
require_once dirname(__FILE__).'/question_result_types/hotspot_question.class.php';

abstract class QuestionResult
{
	private $results;
	private $clo_question;
	private $formvalidator;
	
	private $user_answers;
	private $clo_answers;
	
	private $edit_rights;
	
	private $question_nr;
	private $uaid;
	
	function QuestionResult($formvalidator, $q_results, $clo_question, $edit_rights = 0, $question_nr, $uaid) 
	{
		$this->results = $q_results;
		$this->clo_question = $clo_question;
		$this->formvalidator = $formvalidator;
		$this->edit_rights = $edit_rights;
		$this->question_nr = $question_nr;
		$this->uaid = $uaid;
		$this->init();
	}
	
	function init()
	{
	}
	
	function get_question()
	{
		return RepositoryDataManager :: get_instance()->retrieve_learning_object($this->clo_question->get_ref());
	}
	
	function get_clo_question()
	{
		return $this->clo_question;
	}
	
	function get_edit_rights()
	{
		return $this->edit_rights;
	}
	
	function get_results()
	{
		return $this->results;
	}
	
	function get_user_assessment_id()
	{
		return $this->uaid;
	}
	
	function add_feedback_controls()
	{
		$result = $this->results[0];
		$feedback_id = $result->get_feedback();
			
		if ($this->results != null && !$feedback_id)
		{
			$this->formvalidator->addElement('html', '<h4>'.Translation :: get("AddFeedback").'</h4>');
			
			$result = $this->results[0];
			$this->formvalidator->addElement('hidden', 'ex_'.$this->get_clo_question()->get_id(), '');
			$buttons[] = $this->formvalidator->createElement('text', 'ex'.$this->get_clo_question()->get_id().'_name', null, array('DISABLED=DISABLED', 'style="height: 19px;"'));
			$buttons[] = $this->formvalidator->createElement('style_submit_button', 'feedback_'.$this->get_clo_question()->get_id(), Translation :: get('Select'), array('class' => 'positive'));

			$this->formvalidator->addGroup($buttons, 'buttons', '<div style="padding-top: 4px;">' . Translation :: get('SelectedFeedback') . '</div>', '&nbsp;', false);
		}
	}
	
	function add_score_controls($max_score)
	{
		for ($i = -$max_score; $i <= $max_score; $i++)
		{
			$values[$i] = $i;
		}
		if ($this->results != null)
		{
			$result = $this->results[0];
			$score = $this->formvalidator->createElement('select', 'score'.$this->get_clo_question()->get_id(), Translation :: get('ChangeScore'), $values);
			$defaults['score'.$this->get_clo_question()->get_id()] = 0;
			$this->formvalidator->addElement($score);
		}
		//print_r($defaults);
		$this->formvalidator->setDefaults($defaults);
	}
	
	function display_question_header($show_correct_answer = true)
	{
		$learning_object = $this->get_question();
		//$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$learning_object->get_icon_name().'.png);">';
		//$html[] = '<div class="title">';
		$html[] = '<div class="question">';
		$html[] = '<div class="title" style="padding: 5px 5px 5px 35px; border:1px solid grey; background: #e6e6e6 url('. Theme :: get_common_image_path(). 'learning_object/' .$learning_object->get_icon_name().'.png) no-repeat; background-position: 5px 2px; height: 16px;">';
		$html[] = Translation :: get('Question').' '. $this->question_nr . ': ' .$learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description" style="padding-left: 35px; padding-right: 35px;">';
		$description = $learning_object->get_description();
		
		/*if($description != '<p>&#160;</p>')
			$html[] = '<div style="font-style: italic; ">' . $description . '</div>';
		else
			$html[] = '<br />';*/
		
		if($description != '<p>&#160;</p>' && count($description) > 0 )
			$html[] = '<div style="font-style: italic; ">' . $description . '</div>';
		else
			$html[] = '<br />';
		
		$html[] = '<table border="0" style="width: 100%">';
		$html[] = '<tr>';
		
		if($show_correct_answer)
		{
			$html[] = '<td style="width: 50%">' . Translation :: get('YourAnswer') . '</td>';
			$html[] = '<td style="width: 50%">' . Translation :: get('CorrectAnswer') . '</td>';
		}
		else
		{
			$html[] = '<td style="width: 100%">' . Translation :: get('YourAnswer') . '</td>';
		}
		

		$html[] = '</tr><tr>';
		
		$this->formvalidator->addElement('html', implode("\n", $html));
	}
	
	function display_score($score_line)
	{
		//$html[] = '<div class="description">';
		$html[] = '</tr></table>';
		if($score_line != '')
			$html[] = '<br /><b>' . $score_line. '</b><br/>';
		//$html[] = '</div>';
		$this->formvalidator->addElement('html', implode("\n", $html));
	}
	
	function display_feedback()
	{
		$html[] = '<div style="border-top: 1px solid lightgrey; margin-top: 12px;"><h4>' . Translation :: get('Feedback') . '</h4>';
		if ($this->results != null)
		{
			$result = $this->results[0];
			$feedback_id = $result->get_feedback();
			if ($feedback_id != null && $feedback_id != 0)
			{
				$feedback_lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($feedback_id, 'feedback');
				//$html[] =  $feedback_lo->get_title().'<br/><br/>'.$feedback_lo->get_description().$this->render_attachments($feedback_lo);
				
				$html[] = '<div class="feedback" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$feedback_lo->get_icon_name().$icon_suffix.'.png);">';
				$html[] = '<div class="title">';
				$html[] = $feedback_lo->get_title();
				$html[] = '<span class="publication_info">';
				$html[] = '</span>';
				$html[] = '</div>';
				$html[] = '<div class="description">';
				$html[] = $feedback_lo->get_description();
				$html[] = $this->render_attachments($feedback_lo);
				$html[] = '</div>';
				$html[] = '<div style="float: right;">';
				$html[] = $this->render_feedback_actions();
				$html[] = '</div>';
				$html[] = '<div class="clear">&nbsp;</div>';
				$html[] = '</div>';
				
			}
			else
				$html[] = Translation :: get('NoFeedback');
		}
		else
		{
			$html[] = Translation :: get('QuestionNotAnswered');
		}
		
		$html[] = '</div><br />';
			
		$this->formvalidator->addElement('html', implode("\n", $html));
	}
	
	function render_feedback_actions()
	{
		$quest = $this->get_question();
	
		if($this->formvalidator->get_component()->is_allowed(DELETE_RIGHT))
		{ 
			$actions[] = array(
				'href' => $this->formvalidator->get_component()->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EDIT_QUESTION_FEEDBACK, AssessmentTool :: PARAM_QUESTION_ATTEMPT => $quest->get_id(), AssessmentTool :: PARAM_USER_ASSESSMENT => Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT))), 
				'label' => Translation :: get('Edit'), 
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);
			
			$actions[] = array(
				'href' => $this->formvalidator->get_component()->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_DELETE_QUESTION_FEEDBACK, AssessmentTool :: PARAM_QUESTION_ATTEMPT => $quest->get_id(), AssessmentTool :: PARAM_USER_ASSESSMENT => Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT))), 
				'label' => Translation :: get('Delete'), 
				'img' => Theme :: get_common_image_path().'action_delete.png'
			);
			
			return DokeosUtilities :: build_toolbar($actions);
		}
	}
	
	function render_attachments($object)
	{
		if ($object->supports_attachments())
		{
			$attachments = $object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				$html[] = '<h4>Attachments</h4>';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				$html[] = '<ul>';
				foreach ($attachments as $attachment)
				{
					$html[] = '<li><a href="' . $this->formvalidator->get_component()->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_ATTACHMENT, Tool :: PARAM_OBJECT_ID => $attachment->get_id())) . '"><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$attachment->get_title().'</a></li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
	}
	
	/*function render_attachments($object)
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
	}*/
	
	function display_answers($answer_lines = null, $correct_answer_lines = null, $numbered = true, $use_list = true)
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
	
	static function create_question_result($formvalidator, $clo_question, $q_results, $edit_rights, $question_nr, $pid)
	{
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		switch ($question->get_type())
		{
			case 'open_question':
				return new OpenQuestionResult($formvalidator, $q_results, $clo_question, $edit_rights, $question_nr, $pid);
			case 'multiple_choice_question':
				return new MultipleChoiceQuestionResult($formvalidator, $q_results, $clo_question, $edit_rights, $question_nr, $pid);
			case 'matching_question':
				return new MatchingQuestionResult($formvalidator, $q_results, $clo_question, $edit_rights, $question_nr, $pid);
			case 'fill_in_blanks_question':
				return new FillInBlanksQuestionResult($formvalidator, $q_results, $clo_question, $edit_rights, $question_nr, $pid);
			case 'rating_question':
				return new ScoreQuestionResult($formvalidator, $q_results, $clo_question, $edit_rights, $question_nr, $pid);
			case 'hotspot_question':
				return new HotspotQuestionResult($formvalidator, $q_results, $clo_question, $edit_rights, $question_nr, $pid);
			default:
				return null;
		}
	}
	
	abstract function display_exercise();
	
	abstract function display_survey();
	
	abstract function display_assignment();
}
?>