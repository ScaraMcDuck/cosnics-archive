<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/installwizardpage.class.php';
/**
 * Page in the install wizard in which some config settings are asked to the
 * user.
 */
class SettingsInstallWizardPage extends InstallWizardPage
{
	function get_title()
	{
		return get_lang('CfgSetting');
	}
	function get_info()
	{
		return get_lang('ConfigSettingsInfo');
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('select', 'platform_language', get_lang("MainLang"), $this->get_language_folder_list());
		$this->addElement('text', 'platform_url', get_lang("DokeosURL"), array ('size' => '40'));
		$this->addRule('platform_url', get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule('platform_url', 'AddTrailingSlash', 'regex', '/^.*\/$/');
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
		$this->addGroup($encrypt, 'encrypt_password', get_lang("EncryptUserPass"), '&nbsp;', false);
		$self_reg[] = & $this->createElement('radio', 'self_reg', null, get_lang('Yes'), 1);
		$self_reg[] = & $this->createElement('radio', 'self_reg', null, get_lang('No'), 0);
		$this->addGroup($self_reg, 'self_reg', get_lang("AllowSelfReg"), '&nbsp;', false);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['platform_language'] = $this->exportValue('platform_language');
		$urlAppendPath = str_replace('/install/index.php', '', $_SERVER['PHP_SELF']);
		$defaults['platform_url'] = 'http://'.$_SERVER['HTTP_HOST'].$urlAppendPath.'/';
		$defaults['admin_email'] = $_SERVER['SERVER_ADMIN'];
		$email_parts = explode('@',$defaults['admin_email']);
		if($email_parts[1] == 'localhost')
		{
			$defaults['admin_email'] .= '.localdomain';
		}
		$defaults['admin_lastname'] = 'Doe';
		$defaults['admin_firstname'] = mt_rand(0,1)?'John':'Jane';
		$defaults['admin_username'] = 'admin';
		$defaults['admin_password'] = substr(md5(time()), 0, 8);
		$defaults['platform_name'] = get_lang('MyDokeos');
		$defaults['organization_name'] = get_lang('Dokeos');
		$defaults['organization_url'] = 'http://www.dokeos.com';
		$defaults['self_reg'] = 1;
		$defaults['encrypt_password'] = 1;
		$this->setDefaults($defaults);
	}
	
	function get_language_folder_list()
	{
		$path = dirname(__FILE__).'/../../../../../../main/lang';
		$list = FileSystem::get_directory_content($path, Filesystem::LIST_DIRECTORIES, false);
		$language_list = array();
		foreach($list as $index => $language)
		{
			if ($language == '.' || $language == '..' || $language == '.svn')
				continue;
			$language_list[$language] = $language;
		}
		return $language_list;
	}
}
?>
