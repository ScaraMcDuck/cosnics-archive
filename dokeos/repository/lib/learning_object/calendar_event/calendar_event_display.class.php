<?php
/**
 * @package repository.learningobject
 * @subpackage calendar_event
 */
class CalendarEventDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		$object = $this->get_learning_object();
		$append = '<div class="calendar_event_range" style="margin-top: 1em;"">'.htmlentities(get_lang('From').' '.date('r',$object->get_start_date()).' '.get_lang('Until').' '.date('r',$object->get_end_date())).'</div>';
		return preg_replace('|</div>\s*$|s', $append.'</div>', $html);
	}
}
?>