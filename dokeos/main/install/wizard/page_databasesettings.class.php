<?php
/**
 * @package main
 * @subpackage install
 */
/**
 * Class for database settings page
 * Displays a form where the user can enter the installation settings
 * regarding the databases - login and password, names, prefixes, single
 * or multiple databases, tracking or not...
 */
class Page_DatabaseSettings extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('DBSetting');
	}
	function get_info()
	{
		return get_lang('DBSettingIntro');
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('text', 'database_host', get_lang("DBHost"), array ('size' => '40'));
		$this->addRule('database_host', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_username', get_lang("DBLogin"), array ('size' => '40'));
		$this->addElement('password', 'database_password', get_lang("DBPassword"), array ('size' => '40'));
		$this->addRule(array('database_host','database_username','database_password'),get_lang('CouldNotConnectToDatabase'),new ValidateDatabaseConnection());

		$this->addElement('text', 'database_name', get_lang('DatabaseName'), array ('size' => '40'));
		$this->addRule('database_name', 'ThisFieldIsRequired', 'required');
		$this->addRule('database_name', 'OnlyCharactersNumbersUnderscoresAndHyphens', 'regex', '/^[a-z][a-z0-9_-]+$/');

		$enable_tracking[] = $this->createElement('radio', 'enable_tracking', null, get_lang("Yes"), 1);
		$enable_tracking[] = $this->createElement('radio', 'enable_tracking', null, get_lang("No"), 0);
		$this->addGroup($enable_tracking, 'tracking', get_lang("EnableTracking"), '&nbsp;', false);

		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
class ValidateDatabaseConnection extends HTML_QuickForm_Rule
{
	public function validate($parameters)
	{
		$db_host = $parameters[0];
		$db_user = $parameters[1];
		$db_password = $parameters[2];
		//TODO use database abstraction here
		if(mysql_connect($db_host,$db_user,$db_password))
		{
			return true;
		}
		return false;
	}
}
?>