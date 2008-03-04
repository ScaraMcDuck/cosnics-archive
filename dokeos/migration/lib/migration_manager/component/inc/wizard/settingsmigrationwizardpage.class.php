<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../migrationdatamanager.class.php'; 
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
	
	//TODO: Add support for FTP
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('text', 'old_directory', Translation :: get_lang('old_directory'), array ('size' => '40'));
		$this->addRule('old_directory', 'ThisFieldIsRequired', 'required');

		$exports = $this->controller->exportValues();
		
		$this->addRule(array('old_directory', $exports['old_system']),Translation :: get_lang('CouldNotVerifySettings'), new ValidateSettings());

		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get_lang('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['old_directory'] = '/home/svennie/sites/dokeos/';
		$this->setDefaults($defaults);
	}
}

class ValidateSettings extends HTML_QuickForm_Rule
{
	public function validate($parameters)
	{
		$dmgr = MigrationDataManager :: getInstance($parameters[1], $parameters[0]);
		return $dmgr->validate_settings();
	}
}
?>
