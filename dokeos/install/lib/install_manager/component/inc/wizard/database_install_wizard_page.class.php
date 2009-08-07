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
		
		$this->addElement('category', Translation :: get('Database'));
		$this->addElement('text', 'database_driver', Translation :: get('DBDriver'), array ('size' => '40'));
		$this->addElement('text', 'database_host', Translation :: get('DBHost'), array ('size' => '40'));
		$this->addElement('text', 'database_name', Translation :: get('DatabaseName'), array ('size' => '40'));
		$this->addElement('category');
		
		$this->addElement('category', Translation :: get('Credentials'));
		$this->addElement('text', 'database_username', Translation :: get('DBLogin'), array ('size' => '40'));
		$this->addElement('password', 'database_password', Translation :: get('DBPassword'), array ('size' => '40'));
		$this->addElement('category');
		
		$this->addRule('database_host', 'ThisFieldIsRequired', 'required');
		$this->addRule('database_driver', 'ThisFieldIsRequired', 'required');
		$this->addRule('database_name', 'ThisFieldIsRequired', 'required');
		$this->addRule('database_name', 'OnlyCharactersNumbersUnderscoresAndHyphens', 'regex', '/^[a-z][a-z0-9_-]+$/');
		$this->addRule(array('database_driver', 'database_host', 'database_username', 'database_password'),Translation :: get('CouldNotConnectToDatabase'), new ValidateDatabaseConnection());
		
		$buttons = array();
		$buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous'), array('class' => 'normal previous'));
		$buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		$this->setDefaultAction($this->getButtonName('next'));
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
			//MDB2 :: disconnect();
			return false;
		}
		else
		{
			//MDB2 :: disconnect();
			return true;
		}
	}
}
?>