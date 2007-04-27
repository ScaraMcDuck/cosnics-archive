<?php
/**
 * @package repository.learningobject
 * @subpackage profile
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/profile.class.php';

class ProfileForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_html_editor(Profile :: PROPERTY_ADDRESS, get_lang('Address'));
		$this->add_textfield(Profile :: PROPERTY_PHONE, get_lang('Phone'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_FAX, get_lang('Fax'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_MAIL, get_lang('Mail'), true,'size="40" style="width: 100%;"');
		$this->add_html_editor(Profile :: PROPERTY_COMPETENCES, get_lang('Competences'));
		$this->add_html_editor(Profile :: PROPERTY_DIPLOMAS, get_lang('Diplomas'));
		$this->add_html_editor(Profile :: PROPERTY_TEACHING, get_lang('Teaching'));
		$this->add_html_editor(Profile :: PROPERTY_OPEN, get_lang('Open'));
		$this->add_textfield(Profile :: PROPERTY_SKYPE, get_lang('Skype'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_MSN, get_lang('Msn'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_AIM, get_lang('Aim'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_YIM, get_lang('Yim'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_ICQ, get_lang('Icq'), true,'size="40" style="width: 100%;"');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->add_html_editor(Profile :: PROPERTY_ADDRESS, get_lang('Address'));
		$this->add_textfield(Profile :: PROPERTY_PHONE, get_lang('Phone'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_FAX, get_lang('Fax'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_MAIL, get_lang('Mail'), true,'size="40" style="width: 100%;"');
		$this->add_html_editor(Profile :: PROPERTY_COMPETENCES, get_lang('Competences'));
		$this->add_html_editor(Profile :: PROPERTY_DIPLOMAS, get_lang('Diplomas'));
		$this->add_html_editor(Profile :: PROPERTY_TEACHING, get_lang('Teaching'));
		$this->add_html_editor(Profile :: PROPERTY_OPEN, get_lang('Open'));
		$this->add_textfield(Profile :: PROPERTY_SKYPE, get_lang('Skype'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_MSN, get_lang('Msn'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_AIM, get_lang('Aim'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_YIM, get_lang('Yim'), true,'size="40" style="width: 100%;"');
		$this->add_textfield(Profile :: PROPERTY_ICQ, get_lang('Icq'), true,'size="40" style="width: 100%;"');
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
		}
		
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$object = new Profile();
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
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
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
		return parent :: update_learning_object();
	}
}
?>