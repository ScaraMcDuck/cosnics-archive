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
	private $application;
	
	public function PublicationFormPublisherWizardPage($name,$parent, $application)
	{
		parent :: PublisherWizardPage($name, $parent);
		$this->application = $application;
	}
	
	function get_title()
	{
		return Application :: application_to_class($this->application) . ' ' . Translation :: get('PublicationForm');
	}
	
	function get_info()
	{
		return Translation :: get('PublicationFormInfo');
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;

		$application = $this->application;
		

		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}
}
?>