<?php

abstract class QuestionDisplay 
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
	
	abstract function add_to($formvalidator);
	
	function get_answers($question_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		while($clo_answer = $clo_answers->next_result())
		{
			$answers[] = array(
				'answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'),
			    'score' => $clo_answer->get_score()
			);
		}
		
		return $answers;
	}
	
	function display_learning_object($learning_object)
	{
		$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path(). 'learning_object/' .$learning_object->get_icon_name().'.png);">';
		$html[] = '<div class="title">';
		$html[] = $learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $learning_object->get_description();
		$html[] = $this->render_attachments($learning_object);
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	function render_attachments($learning_object)
	{
		if ($learning_object->supports_attachments())
		{
			$attachments = $learning_object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				$html[] = '<ul class="attachments_list">';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = LearningObjectDisplay :: factory($attachment);
					$html[] = '<li><img src="'.Theme :: get_common_img_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}
}
?>