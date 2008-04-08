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
			$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'home_medium.gif);">';
			$html[] = '<div class="title">'. Translation :: get('Address') .'</div>';
			$html[] = $object->get_address();
			$html[] = '</div>';
		}

		if ($object->get_mail() != '' || $object->get_phone() != '' || $object->get_fax() != '' || $object->get_skype() != '' || $object->get_msn() != '' || $object->get_aim() != '' || $object->get_yim() != '' || $object->get_icq() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'contact.gif);">';
			$html[] = '<div class="title">'. Translation :: get('Contact') .'</div>';
			if ($object->get_mail() != '') $html[] = Translation :: get('Mail') . ': <a href="mailto:' . $object->get_mail() . '">' . $object->get_mail() . '</a><br/>';
			if ($object->get_phone() != '') $html[] = Translation :: get('TelShort') . ': ' . $object->get_phone() . '<br/>';
			if ($object->get_fax() != '') $html[] = Translation :: get('FaxShort') . ': ' . $object->get_fax() . '<br/>';
			if ($object->get_skype() != '') $html[] = Translation :: get('Skype') . ': ' . $object->get_skype() . '<br/>';
			if ($object->get_msn() != '') $html[] = Translation :: get('Msn') . ': ' . $object->get_msn() . '<br/>';
			if ($object->get_yim() != '') $html[] = Translation :: get('Yim') . ': ' . $object->get_yim() . '<br/>';
			if ($object->get_aim() != '') $html[] = Translation :: get('Aim') . ': ' . $object->get_aim() . '<br/>';
			if ($object->get_icq() != '') $html[] = Translation :: get('Icq') . ': ' . $object->get_icq() . '<br/>';
			$html[] = '</div>';
		}

		if ($object->get_competences() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'competences.gif);">';
			$html[] = '<div class="title">'. Translation :: get('Competences') .'</div>';
			$html[] = $object->get_competences();
			$html[] = '</div>';
		}

		if ($object->get_diplomas() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'diplomas.gif);">';
			$html[] = '<div class="title">'. Translation :: get('Diplomas') .'</div>';
			$html[] = $object->get_diplomas();
			$html[] = '</div>';
		}

		if ($object->get_teaching() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'teaching.gif);">';
			$html[] = '<div class="title">'. Translation :: get('Teaching') .'</div>';
			$html[] = $object->get_teaching();
			$html[] = '</div>';
		}

		if ($object->get_open() != '')
		{
			$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'open.gif);">';
			$html[] = '<div class="title">'. Translation :: get('Open') .'</div>';
			$html[] = $object->get_open();
			$html[] = '</div>';
		}

		return implode("\n", $html);
	}
	function get_description()
	{
		$object = $this->get_learning_object();
		$html = array();
		if($object->get_picture())
		{
			$user_id = $object->get_owner_id();
			$udm = UsersDataManager :: get_instance();
			$user = $udm->retrieve_user($user_id);
			if($user->has_picture())
			{
				$html[] = '<img src="'.$user->get_full_picture_url().'" alt="'.$user->get_fullname().'" style="position:absolute;right: 20px;border:1px solid black;max-width:150px;"/>';
			}
		}
		$html[] = '<div class="description">';
		$html[] = $object->get_description();
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>