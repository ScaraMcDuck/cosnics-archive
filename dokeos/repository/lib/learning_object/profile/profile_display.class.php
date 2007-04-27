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
		
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/home_medium.gif);">';
		$html[] = '<div class="title">'. get_lang('Address') .'</div>';
		$html[] = $object->get_address();
		$html[] = '</div>';
		
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/contact.gif);">';
		$html[] = '<div class="title">'. get_lang('Contact') .'</div>';
		$html[] = get_lang('TelShort') . ': ' . $object->get_phone() . '<br/>';
		$html[] = get_lang('FaxShort') . ': ' . $object->get_fax() . '<br/>';
		$html[] = get_lang('Skype') . ': ' . $object->get_skype() . '<br/>';
		$html[] = get_lang('Msn') . ': ' . $object->get_msn() . '<br/>';
		$html[] = get_lang('Yim') . ': ' . $object->get_yim() . '<br/>';
		$html[] = get_lang('Aim') . ': ' . $object->get_aim() . '<br/>';
		$html[] = get_lang('Icq') . ': ' . $object->get_icq() . '<br/>';
		$html[] = '</div>';
		
		
		
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/competences.gif);">';
		$html[] = '<div class="title">'. get_lang('Competences') .'</div>';
		$html[] = $object->get_competences();
		$html[] = '</div>';
		
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/diplomas.gif);">';
		$html[] = '<div class="title">'. get_lang('Diplomas') .'</div>';
		$html[] = $object->get_diplomas();
		$html[] = '</div>';
		
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/teaching.gif);">';
		$html[] = '<div class="title">'. get_lang('Teaching') .'</div>';
		$html[] = $object->get_teaching();
		$html[] = '</div>';
		
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/open.gif);">';
		$html[] = '<div class="title">'. get_lang('Open') .'</div>';
		$html[] = $object->get_open();
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}
?>