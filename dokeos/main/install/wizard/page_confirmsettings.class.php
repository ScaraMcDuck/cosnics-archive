<?php
/**
 * @package main
 * @subpackage install
 */
// Class for final overview page
class Page_ConfirmSettings extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('LastCheck');
	}
	function get_info()
	{
		return 'Here are the values you entered
											<br>
											<b>Print this page to remember your password and other settings</b>';

	}
	function buildForm()
	{
		$wizard = $this->controller;
		$values = $wizard->exportValues();
		$this->addElement('static', 'confirm_platform_language', get_lang("MainLang"), $values['platform_language']);
		$this->addElement('static', 'confirm_platform_url', get_lang("DokeosURL"), $values['platform_url']);
		$this->addElement('static', 'confirm_admin_email', get_lang("AdminEmail"), $values['admin_email']);
		$this->addElement('static', 'confirm_admin_lastname', get_lang("AdminLastName"), $values['admin_lastname']);
		$this->addElement('static', 'confirm_admin_firstname', get_lang("AdminFirstName"), $values['admin_firstname']);
		$this->addElement('static', 'confirm_admin_phone', get_lang("AdminPhone"), $values['admin_phone']);
		$this->addElement('static', 'confirm_admin_username', get_lang("AdminLogin"), $values['admin_username']);
		$this->addElement('static', 'confirm_admin_password', get_lang("AdminPass"), $values['admin_password']);
		$this->addElement('static', 'confirm_platform_name', get_lang("CampusName"), $values['platform_name']);
		$this->addElement('static', 'confirm_organization_name', get_lang("InstituteShortName"), $values['organization_name']);
		$this->addElement('static', 'confirm_organization_url', get_lang("InstituteURL"), $values['organization_url']);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>
