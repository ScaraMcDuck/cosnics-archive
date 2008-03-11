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
	
	function next_step_info()
	{
		return Translation :: get_lang('Users_info');
	}

	function buildForm()
	{
		$exports = $this->controller->exportValues();
		$this->_formBuilt = true;
		$this->addElement('text', 'old_directory', Translation :: get_lang('old_directory'), array ('size' => '40'));
		$this->addRule('old_directory', 'ThisFieldIsRequired', 'required');

		$this->addElement('checkbox', 'migrate_users', '', Translation :: get_lang('migrate_users'), 'onclick=\'users_clicked()\'');
		$this->addElement('checkbox', 'migrate_personal_agendas', '', Translation :: get_lang('migrate_personal_agendas'), 'onclick=\'personal_agendas_clicked()\' style=\'margin-left: 20px;\'');
		$this->addElement('checkbox', 'migrate_settings', '', Translation :: get_lang('migrate_settings'));
		$this->addElement('checkbox', 'migrate_classes', '', Translation :: get_lang('migrate_classes'));
		$this->addElement('checkbox', 'migrate_courses', '', Translation :: get_lang('migrate_courses'), 'onclick=\'courses_clicked()\'');
		$this->addElement('checkbox', 'migrate_metadata', '', Translation :: get_lang('migrate_metadata'), 'onclick=\'metadata_clicked()\' style=\'margin-left: 20px;\'');
		$this->addElement('checkbox', 'migrate_groups', '', Translation :: get_lang('migrate_groups'), 'onclick=\'groups_clicked()\' style=\'margin-left: 20px;\'');
		$this->addElement('checkbox', 'migrate_announcements', '', Translation :: get_lang('migrate_announcements'), 'onclick=\'announcements_clicked()\' style=\'margin-left: 20px;\'');
		$this->addElement('checkbox', 'migrate_calendar_events', '', Translation :: get_lang('migrate_calendar_events'), 'onclick=\'calendar_events_clicked()\' style=\'margin-left: 20px;\'');
		$this->addElement('checkbox', 'migrate_documents', '', Translation :: get_lang('migrate_documents'), 'onclick=\'documents_clicked()\' style=\'margin-left: 20px;\'');
		$this->addElement('checkbox', 'migrate_links', '', Translation :: get_lang('migrate_links'), 'onclick=\'links_clicked()\' style=\'margin-left: 20px;\'');
		
		$this->addElement('checkbox', 'migrate_deleted_files', '', 
			Translation :: get_lang('migrate_deleted_files'), 'onclick=\'deleted_files_clicked("' . 
			Translation :: get_lang('confirm_deleted_files'). '")\' style=\'margin-top: 20px;\'');
		
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
		$defaults['migrate_users'] = '1';
		$defaults['migrate_personal_agendas'] = '1';
		$defaults['migrate_settings'] = '1';
		$defaults['migrate_classes'] = '1';
		$defaults['migrate_courses'] = '1';
		$defaults['migrate_groups'] = '1';
		$defaults['migrate_metadata'] = 1;
		$defaults['migrate_announcements'] = '1';
		$defaults['migrate_calendar_events'] = '1';
		$defaults['migrate_documents'] = '1';
		$defaults['migrate_links'] = '1';
		$defaults['migrate_deleted_files'] = '0';
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
