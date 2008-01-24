<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/installwizardpage.class.php';
/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class ApplicationInstallWizardPage extends InstallWizardPage
{
	function get_title()
	{
		return get_lang('AppSetting');
	}
	
	function get_info()
	{
		return get_lang('AppSettingIntro');
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;

		$applications = Application::load_all();
		foreach($applications as $application)
		{
			$checkbox_name = 'install_' . $application;
			$this->addElement('checkbox', $checkbox_name, '', get_lang(Application::application_to_class($application)));
			$appDefaults[$checkbox_name] = '1';
		}
		
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}
}
?>