<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/installwizardpage.class.php';
/**
 * This form can be used to let the user select the action.
 */
class LanguageInstallWizardPage extends InstallWizardPage
{
	function get_title()
	{
		return get_lang('WelcomeToDokeosInstaller');
	}
	
	function get_info()
	{
		return 'Please select the language you\'d like to use while installing:';
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('select', 'install_language', get_lang('InstallationLanguage'), $this->get_language_folder_list());
		$buttons[0] = HTML_QuickForm :: createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($buttons, 'buttons', '', '&nbsp', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
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
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['install_language'] = 'english';
		$defaults['platform_language'] = 'english';
		$this->setDefaults($defaults);
	}
}
?>