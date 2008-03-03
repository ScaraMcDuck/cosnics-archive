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
		return Translation :: get_lang('CfgSetting');
	}
	function get_info()
	{
		return Translation :: get_lang('ConfigSettingsInfo');
	}
	function buildForm()
	{
		$this->set_lang($this->controller->exportValue('page_language', 'install_language'));
		$this->_formBuilt = true;
		$this->addElement('select', 'platform_language', Translation :: get_lang("MainLang"), $this->get_language_folder_list());
		$this->addElement('text', 'platform_url', Translation :: get_lang("DokeosURL"), array ('size' => '40'));
		$this->addRule('platform_url', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule('platform_url', 'AddTrailingSlash', 'regex', '/^.*\/$/');
		$this->addElement('text', 'admin_email', Translation :: get_lang("AdminEmail"), array ('size' => '40'));
		$this->addRule('admin_email', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule('admin_email', Translation :: get_lang('WrongEmail'), 'email');
		$this->addElement('text', 'admin_surname', Translation :: get_lang("AdminLastName"), array ('size' => '40'));
		$this->addRule('admin_surname', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_firstname', Translation :: get_lang("AdminFirstName"), array ('size' => '40'));
		$this->addRule('admin_firstname', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_phone', Translation :: get_lang("AdminPhone"), array ('size' => '40'));
		$this->addElement('text', 'admin_username', Translation :: get_lang("AdminLogin"), array ('size' => '40'));
		$this->addRule('admin_username', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_password', Translation :: get_lang("AdminPass"), array ('size' => '40'));
		$this->addRule('admin_password', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'platform_name', Translation :: get_lang("CampusName"), array ('size' => '40'));
		$this->addRule('platform_name', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'organization_name', Translation :: get_lang("InstituteShortName"), array ('size' => '40'));
		$this->addRule('organization_name', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'organization_url', Translation :: get_lang("InstituteURL"), array ('size' => '40'));
		$this->addRule('organization_url', Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$encrypt[] = $this->createElement('radio', 'encrypt_password', null, Translation :: get_lang('Yes'), 1);
		$encrypt[] = $this->createElement('radio', 'encrypt_password', null, Translation :: get_lang('No'), 0);
		$this->addGroup($encrypt, 'encrypt_password', Translation :: get_lang("EncryptUserPass"), '&nbsp;', false);
		$self_reg[] = $this->createElement('radio', 'self_reg', null, Translation :: get_lang('Yes'), 1);
		$self_reg[] = $this->createElement('radio', 'self_reg', null, Translation :: get_lang('No'), 0);
		$this->addGroup($self_reg, 'self_reg', Translation :: get_lang("AllowSelfReg"), '&nbsp;', false);
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get_lang('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['platform_language'] = $this->controller->exportValue('page_language', 'install_language');
		$urlAppendPath = str_replace('/install/index.php', '', $_SERVER['PHP_SELF']);
		$defaults['platform_url'] = 'http://'.$_SERVER['HTTP_HOST'].$urlAppendPath.'/';
		$defaults['admin_email'] = $_SERVER['SERVER_ADMIN'];
		$email_parts = explode('@',$defaults['admin_email']);
		if($email_parts[1] == 'localhost')
		{
			$defaults['admin_email'] .= '.localdomain';
		}
		$defaults['admin_surname'] = 'Doe';
		$defaults['admin_firstname'] = mt_rand(0,1)?'John':'Jane';
		$defaults['admin_username'] = 'admin';
		$defaults['admin_password'] = substr(md5(time()), 0, 8);
		$defaults['platform_name'] = Translation :: get_lang('MyDokeos');
		$defaults['organization_name'] = Translation :: get_lang('Dokeos');
		$defaults['organization_url'] = 'http://www.dokeos.com';
		$defaults['self_reg'] = 0;
		$defaults['encrypt_password'] = 1;
		$this->setDefaults($defaults);
	}
	
	function get_language_folder_list()
	{
		$path = dirname(__FILE__).'/../../../../../../languages';
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
