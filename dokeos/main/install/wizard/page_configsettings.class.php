<?php
/**
 * @package main
 * @subpackage install
 */
class Page_ConfigSettings extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('CfgSetting');
	}
	function get_info()
	{
		return 'The following values will be written into your configuration file <b>main/inc/conf/claro_main.conf.php</b>';
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$languages = array ();
		$languages['dutch'] = 'dutch';
		$this->addElement('select', 'platform_language', get_lang("MainLang"), get_language_folder_list());
		$this->addElement('text', 'platform_url', get_lang("DokeosURL"), array ('size' => '40'));
		$this->addRule('platform_url', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_email', get_lang("AdminEmail"), array ('size' => '40'));
		$this->addRule('admin_email', get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule('admin_email', get_lang('WrongEmail'), 'email');
		$this->addElement('text', 'admin_lastname', get_lang("AdminLastName"), array ('size' => '40'));
		$this->addRule('admin_lastname', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_firstname', get_lang("AdminFirstName"), array ('size' => '40'));
		$this->addRule('admin_firstname', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_phone', get_lang("AdminPhone"), array ('size' => '40'));
		$this->addElement('text', 'admin_username', get_lang("AdminLogin"), array ('size' => '40'));
		$this->addRule('admin_username', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_password', get_lang("AdminPass"), array ('size' => '40'));
		$this->addRule('admin_password', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'platform_name', get_lang("CampusName"), array ('size' => '40'));
		$this->addRule('platform_name', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'organization_name', get_lang("InstituteShortName"), array ('size' => '40'));
		$this->addRule('organization_name', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'organization_url', get_lang("InstituteURL"), array ('size' => '40'));
		$this->addRule('organization_url', get_lang('ThisFieldIsRequired'), 'required');
		$encrypt[] = & $this->createElement('radio', 'encrypt_password', null, get_lang('Yes'), 1);
		$encrypt[] = & $this->createElement('radio', 'encrypt_password', null, get_lang('No'), 0);
		$this->addGroup($encrypt, 'tracking', get_lang("EncryptUserPass"), '&nbsp;', false);
		$self_reg[] = & $this->createElement('radio', 'self_reg', null, get_lang('Yes'), 1);
		$self_reg[] = & $this->createElement('radio', 'self_reg', null, get_lang('No'), 0);
		$this->addGroup($self_reg, 'tracking', get_lang("AllowSelfReg"), '&nbsp;', false);
		$self_reg_teacher[] = & $this->createElement('radio', 'self_reg_teacher', null, get_lang('Yes'), 1);
		$self_reg_teacher[] = & $this->createElement('radio', 'self_reg_teacher', null, get_lang('No'), 0);
		$this->addGroup($self_reg_teacher, 'tracking', get_lang("AllowSelfRegProf"), '&nbsp;', false);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>
