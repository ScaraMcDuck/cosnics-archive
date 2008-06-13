<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/install_wizard_page.class.php';
require_once 'MDB2.php';
/**
 * Class for database settings page
 * Displays a form where the user can enter the installation settings
 * regarding the databases - login and password, names, prefixes, single
 * or multiple databases, tracking or not...
 */
class DatabaseInstallWizardPage extends InstallWizardPage
{
	function get_title()
	{
		return Translation :: get('DBSetting');
	}
	function get_info()
	{
		return Translation :: get('DBSettingIntro');
	}
	function buildForm()
	{
		$this->set_lang($this->controller->exportValue('page_language', 'install_language'));
		$this->_formBuilt = true;
		$this->addElement('text', 'database_driver', Translation :: get('DBDriver'), array ('size' => '40'));
		$this->addRule('database_driver', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_host', Translation :: get('DBHost'), array ('size' => '40'));
		$this->addRule('database_host', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_username', Translation :: get('DBLogin'), array ('size' => '40'));
		$this->addElement('password', 'database_password', Translation :: get('DBPassword'), array ('size' => '40'));
		$this->addRule(array('database_driver', 'database_host', 'database_username', 'database_password'),Translation :: get('CouldNotConnectToDatabase'), new ValidateDatabaseConnection());

		$this->addElement('text', 'database_name', Translation :: get('DatabaseName'), array ('size' => '40'));
		$this->addRule('database_name', 'ThisFieldIsRequired', 'required');
		$this->addRule('database_name', 'OnlyCharactersNumbersUnderscoresAndHyphens', 'regex', '/^[a-z][a-z0-9_-]+$/');
		
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['database_driver'] = 'mysql';
		$defaults['database_host'] = 'localhost';
		$defaults['database_name'] = 'lcms';
		//$defaults['enable_tracking'] = 1;
		$this->setDefaults($defaults);
	}
}

class ValidateDatabaseConnection extends HTML_QuickForm_Rule
{
	public function validate($parameters)
	{
		$db_driver = $parameters[0];
		$db_host = $parameters[1];
		$db_user = $parameters[2];
		$db_password = $parameters[3];
		
		$connection_string = $db_driver . '://'. $db_user .':'. $db_password .'@'. $db_host;
		$connection = MDB2 :: connect($connection_string);
		
		if (MDB2 :: isError($connection))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
?>