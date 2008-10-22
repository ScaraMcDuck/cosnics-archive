<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/publisher_wizard_page.class.php';
/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class PublicationFormPublisherWizardPage extends PublisherWizardPage
{
	function get_title()
	{
		return Translation :: get('PublicationForm');
	}
	
	function get_info()
	{
		return Translation :: get('PublicationFormInfo');
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;

		$applications = Application::load_all_from_filesystem(false);
		foreach($applications as $application)
		{
			$this->addElement('checkbox', $application, '', Translation :: get(Application::application_to_class($application)));
			$appDefaults[$application] = '1';
		}
		
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}
}
?>