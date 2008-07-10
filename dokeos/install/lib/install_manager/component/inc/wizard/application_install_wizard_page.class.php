<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/install_wizard_page.class.php';
/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class ApplicationInstallWizardPage extends InstallWizardPage
{
	function get_title()
	{
		return Translation :: get('AppSetting');
	}
	
	function get_info()
	{
		return Translation :: get('AppSettingIntro');
	}
	
	function buildForm()
	{
		$this->set_lang($this->controller->exportValue('page_language', 'install_language'));
		$this->_formBuilt = true;

		$applications = Application::load_all_from_filesystem(false);
		foreach($applications as $application)
		{
			$checkbox_name = 'install_' . $application;
			$this->addElement('checkbox', $checkbox_name, '', Translation :: get(Application::application_to_class($application)));
			$appDefaults[$checkbox_name] = '1';
		}
		
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}
}
?>