<?php
/**
 * @package repository.learningobject
 * @subpackage profile
 *
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/profile.class.php';

class ProfileForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->build_default_form();
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->build_default_form();
	}
	private function build_default_form()
	{
		$this->add_html_editor(Profile :: PROPERTY_ADDRESS, Translation :: get_lang('Address'), false);
		$this->add_textfield(Profile :: PROPERTY_PHONE, Translation :: get_lang('Phone'), false,'size="40"');
		$this->add_textfield(Profile :: PROPERTY_FAX, Translation :: get_lang('Fax'), false,'size="40"');
		$this->add_textfield(Profile :: PROPERTY_MAIL, Translation :: get_lang('Mail'), false,'size="40"');
		$this->addRule(Profile :: PROPERTY_MAIL,Translation :: get_lang('InvalidEmail'),'email');
		$this->add_html_editor(Profile :: PROPERTY_COMPETENCES, Translation :: get_lang('Competences'), false);
		$this->add_html_editor(Profile :: PROPERTY_DIPLOMAS, Translation :: get_lang('Diplomas'), false);
		$this->add_html_editor(Profile :: PROPERTY_TEACHING, Translation :: get_lang('Teaching'), false);
		$this->add_html_editor(Profile :: PROPERTY_OPEN, Translation :: get_lang('Open'), false);
		$this->add_textfield(Profile :: PROPERTY_SKYPE, Translation :: get_lang('Skype'), false,'size="40"');
		$this->add_textfield(Profile :: PROPERTY_MSN, Translation :: get_lang('Msn'), false,'size="40"');
		$this->add_textfield(Profile :: PROPERTY_AIM, Translation :: get_lang('Aim'), false,'size="40"');
		$this->add_textfield(Profile :: PROPERTY_YIM, Translation :: get_lang('Yim'), false,'size="40"');
		$this->add_textfield(Profile :: PROPERTY_ICQ, Translation :: get_lang('Icq'), false,'size="40"');
		$this->addElement('checkbox',Profile::PROPERTY_PICTURE,Translation :: get_lang('IncludeAccountPicture'));

	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[Profile :: PROPERTY_COMPETENCES] = $lo->get_competences();
			$defaults[Profile :: PROPERTY_DIPLOMAS] = $lo->get_diplomas();
			$defaults[Profile :: PROPERTY_TEACHING] = $lo->get_teaching();
			$defaults[Profile :: PROPERTY_OPEN] = $lo->get_open();
			$defaults[Profile :: PROPERTY_PHONE] = $lo->get_phone();
			$defaults[Profile :: PROPERTY_FAX] = $lo->get_fax();
			$defaults[Profile :: PROPERTY_ADDRESS] = $lo->get_address();
			$defaults[Profile :: PROPERTY_MAIL] = $lo->get_mail();
			$defaults[Profile :: PROPERTY_SKYPE] = $lo->get_skype();
			$defaults[Profile :: PROPERTY_MSN] = $lo->get_msn();
			$defaults[Profile :: PROPERTY_YIM] = $lo->get_yim();
			$defaults[Profile :: PROPERTY_AIM] = $lo->get_aim();
			$defaults[Profile :: PROPERTY_ICQ] = $lo->get_icq();
			$defaults[Profile :: PROPERTY_PICTURE] = $lo->get_picture();
		}

		parent :: setDefaults($defaults);
	}

	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		$defaults[Profile :: PROPERTY_COMPETENCES] = $valuearray[3];
		$defaults[Profile :: PROPERTY_DIPLOMAS] = $valuearray[4];
		$defaults[Profile :: PROPERTY_MAIL] = $valuearray[5];
		$defaults[Profile :: PROPERTY_TEACHING] = $valuearray[6];
		$defaults[Profile :: PROPERTY_OPEN] = $valuearray[7];
		$defaults[Profile :: PROPERTY_PHONE] = $valuearray[8];
		$defaults[Profile :: PROPERTY_FAX] = $valuearray[9];
		$defaults[Profile :: PROPERTY_ADDRESS] = $valuearray[10];
		$defaults[Profile :: PROPERTY_SKYPE] = $valuearray[11];
		$defaults[Profile :: PROPERTY_MSN] = $valuearray[12];
		$defaults[Profile :: PROPERTY_YIM] = $valuearray[13];
		$defaults[Profile :: PROPERTY_AIM] = $valuearray[14];
		$defaults[Profile :: PROPERTY_ICQ] = $valuearray[15];
		$defaults[Profile :: PROPERTY_PICTURE] = $valuearray[16];
	
		parent :: set_values($defaults);	
	}

	function create_learning_object()
	{
		$object = new Profile();
		$this->fill_properties($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$this->fill_properties($object);
		return parent :: update_learning_object();
	}
	private function fill_properties($object)
	{
		$object->set_competences($this->exportValue(Profile :: PROPERTY_COMPETENCES));
		$object->set_diplomas($this->exportValue(Profile :: PROPERTY_DIPLOMAS));
		$object->set_teaching($this->exportValue(Profile :: PROPERTY_TEACHING));
		$object->set_open($this->exportValue(Profile :: PROPERTY_OPEN));
		$object->set_phone($this->exportValue(Profile :: PROPERTY_PHONE));
		$object->set_fax($this->exportValue(Profile :: PROPERTY_FAX));
		$object->set_address($this->exportValue(Profile :: PROPERTY_ADDRESS));
		$object->set_mail($this->exportValue(Profile :: PROPERTY_MAIL));
		$object->set_skype($this->exportValue(Profile :: PROPERTY_SKYPE));
		$object->set_msn($this->exportValue(Profile :: PROPERTY_MSN));
		$object->set_yim($this->exportValue(Profile :: PROPERTY_YIM));
		$object->set_aim($this->exportValue(Profile :: PROPERTY_AIM));
		$object->set_icq($this->exportValue(Profile :: PROPERTY_ICQ));
		$object->set_picture($this->exportValue(Profile::PROPERTY_PICTURE));
	}
}
?>
