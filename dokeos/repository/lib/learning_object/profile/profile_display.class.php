<?php
/**
 * $Id: calendar_event_display.class.php 10642 2007-01-10 12:29:07Z bmol $
 * @package repository.learningobject
 * @subpackage profile
 */
/**
 * This class can be used to display calendar events
 */
class ProfileDisplay extends LearningObjectDisplay
{
	// Inherited
	function get_full_html()
	{
		$html = array();
		$html[] = parent :: get_full_html();
		
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$object->get_icon_name().($object->is_latest_version() ? '' : '_na').'.gif);">';
		$html[] = '<div class="title">'. get_lang('Competences') .'</div>';
		$html[] = $object->get_competences();
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}
?>