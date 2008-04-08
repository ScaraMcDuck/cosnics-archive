<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/installwizardpage.class.php';
/**
 * Class for license page
 * Displays the GNU GPL license that has to be accepted to install Dokeos.
 */
class LicenseInstallWizardPage extends InstallWizardPage
{
	function get_title()
	{
		return Translation :: get('Licence');
	}
	function get_info()
	{
		return Translation :: get('DokeosLicenseInfo');
	}
	function buildForm()
	{
		$this->set_lang($this->controller->exportValue('page_language', 'install_language'));
		$this->_formBuilt = true;
		$this->addElement('textarea', 'license', Translation :: get('Licence'), array ('cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style'=>'background-color: white;'));
		$this->addElement('checkbox','license_accept','',Translation :: get('IAccept'));
		$this->addRule('license_accept',Translation :: get('ThisFieldIsRequired'),'required');
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['license'] = implode("\n", file('../documentation/license.txt'));
		$this->setDefaults($defaults);
	}
}
?>