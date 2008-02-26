<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__).'/migrationwizardpage.class.php';
/**
 * Class for database settings page
 * Displays a form where the user can enter the installation settings
 * regarding the databases - login and password, names, prefixes
 * 
 */
class SettingsMigrationWizardPage extends MigrationWizardPage
{
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Setting_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{
		return Translation :: get_lang('Setting_info') . ':';
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('text', 'database_host', Translation :: get_lang('DBHost'), array ('size' => '40'));
		$this->addRule('database_host', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_username', Translation :: get_lang('DBLogin'), array ('size' => '40'));
		$this->addElement('password', 'database_password', Translation :: get_lang('DBPassword'), array ('size' => '40'));
		$this->addElement('text', 'old_directory', Translation :: get_lang('old_directory'), array ('size' => '40'));
		$this->addRule('old_directory', 'ThisFieldIsRequired', 'required');
		
		$this->addRule(array('old_directory'));
		$this->addRule(array('database_host','database_username','database_password'),Translation :: get_lang('CouldNotConnectToDatabase'), new ValidateDatabaseConnection());

		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get_lang('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['database_host'] = 'localhost';
		$this->setDefaults($defaults);
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
