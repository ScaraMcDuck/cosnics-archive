<?php
/**
 * @package repository.learningobject.calendar_event
 */
class CalendarEventDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
		$html[] = '<div class="title">'.$object->get_title().'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div>';
		$html[] = '<br>';
		//TODO change date output to locale date format
		$html[] = '<div class="start_date">'.date('r',$object->get_start_date()).'</div>';
		$html[] = '<div class="end_date">'.date('r',$object->get_end_date()).'</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>