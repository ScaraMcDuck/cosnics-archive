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
		return get_lang('Licence');
	}
	function get_info()
	{
		return get_lang('DokeosLicenseInfo');
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('textarea', 'license', get_lang('Licence'), array ('cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style'=>'background-color: white;'));
		$this->addElement('checkbox','license_accept','',get_lang('IAccept'));
		$this->addRule('license_accept',get_lang('ThisFieldIsRequired'),'required');
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function set_form_defaults()
	{
		$defaults['license'] = implode("\n", file('../documentation/license.txt'));
		$this->setDefaults($defaults);
	}
}
?>