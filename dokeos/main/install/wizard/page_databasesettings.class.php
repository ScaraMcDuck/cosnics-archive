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
		$this->addElement('text', 'database_prefix', get_lang("DbPrefixForm"), array ('size' => '40'));
		$this->addElement('text', 'database_main_db', get_lang("MainDB"), array ('size' => '40'));
		$this->addRule('database_main_db', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_tracking', get_lang("StatDB"), array ('size' => '40'));
		$this->addRule('database_tracking', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_scorm', get_lang("ScormDB"), array ('size' => '40'));
		$this->addRule('database_scorm', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_user', get_lang("UserDB"), array ('size' => '40'));
		$this->addRule('database_user', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_repository', get_lang("RepositoryDatabase"), array ('size' => '40'));
		$this->addRule('database_repository', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_weblcms', get_lang("WeblcmsDatabase"), array ('size' => '40'));
		$this->addRule('database_weblcms', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_personal_calendar', get_lang("PersonalCalendarDatabase"), array ('size' => '40'));
		$this->addRule('database_personal_calendar', 'ThisFieldIsRequired', 'required');
		$enable_tracking[] = & $this->createElement('radio', 'enable_tracking', null, get_lang("Yes"), 1);
		$enable_tracking[] = & $this->createElement('radio', 'enable_tracking', null, get_lang("No"), 0);
		$this->addGroup($enable_tracking, 'tracking', get_lang("EnableTracking"), '&nbsp;', false);
		$several_db[] = & $this->createElement('radio', 'database_single', null, get_lang("One"),1);
		$several_db[] = & $this->createElement('radio', 'database_single', null, get_lang("Several"),0);
		$this->addGroup($several_db, 'db', get_lang("SingleDb"), '&nbsp;', false);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
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
		if(mysql_connect($db_host,$db_user,$db_password))
		{
			return true;
		}
		return false;
	}
}
?>