<?php
/**
 * @package repository.learningobject
 * @subpackage profile
 * 
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
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
		
		if ($object->get_address() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/home_medium.gif);">';
			$html[] = '<div class="title">'. get_lang('Address') .'</div>';
			$html[] = $object->get_address();
			$html[] = '</div>';
		}
		
		if ($object->get_mail() != '' || $object->get_phone() != '' || $object->get_fax() != '' || $object->get_skype() != '' || $object->get_msn() != '' || $object->get_aim() != '' || $object->get_yim() != '' || $object->get_icq() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/contact.gif);">';
			$html[] = '<div class="title">'. get_lang('Contact') .'</div>';
			if ($object->get_mail() != '') $html[] = get_lang('Mail') . ': <a href="mailto:' . $object->get_mail() . '">' . $object->get_mail() . '</a><br/>';
			if ($object->get_phone() != '') $html[] = get_lang('TelShort') . ': ' . $object->get_phone() . '<br/>';
			if ($object->get_fax() != '') $html[] = get_lang('FaxShort') . ': ' . $object->get_fax() . '<br/>';
			if ($object->get_skype() != '') $html[] = get_lang('Skype') . ': ' . $object->get_skype() . '<br/>';
			if ($object->get_msn() != '') $html[] = get_lang('Msn') . ': ' . $object->get_msn() . '<br/>';
			if ($object->get_yim() != '') $html[] = get_lang('Yim') . ': ' . $object->get_yim() . '<br/>';
			if ($object->get_aim() != '') $html[] = get_lang('Aim') . ': ' . $object->get_aim() . '<br/>';
			if ($object->get_icq() != '') $html[] = get_lang('Icq') . ': ' . $object->get_icq() . '<br/>';
			$html[] = '</div>';
		}
		
		if ($object->get_competences() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/competences.gif);">';
			$html[] = '<div class="title">'. get_lang('Competences') .'</div>';
			$html[] = $object->get_competences();
			$html[] = '</div>';
		}
		
		if ($object->get_diplomas() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/diplomas.gif);">';
			$html[] = '<div class="title">'. get_lang('Diplomas') .'</div>';
			$html[] = $object->get_diplomas();
			$html[] = '</div>';
		}
		
		if ($object->get_teaching() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/teaching.gif);">';
			$html[] = '<div class="title">'. get_lang('Teaching') .'</div>';
			$html[] = $object->get_teaching();
			$html[] = '</div>';
		}
		
		if ($object->get_open() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/open.gif);">';
			$html[] = '<div class="title">'. get_lang('Open') .'</div>';
			$html[] = $object->get_open();
			$html[] = '</div>';
		}
		
		return implode("\n", $html);
	}
}
?>