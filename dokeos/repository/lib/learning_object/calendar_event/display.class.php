<?php
/**
 * @package learningobject.calendarevent
 */
class CalendarEventDisplay extends LearningObjectDisplay
{
	public function CalendarEventDisplay(&$object)
	{
		parent :: LearningObjectDisplay($object);
	}
	public function get_full_html()
	{
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
		$html[] = '<div class="title">'.$object->get_title().'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div>';
		$html[] = '<br>';
		$html[] = '<div class="start_date">'.$object->get_start_date().'</div>';
		$html[] = '<div class="end_date">'.$object->get_end_date().'</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>