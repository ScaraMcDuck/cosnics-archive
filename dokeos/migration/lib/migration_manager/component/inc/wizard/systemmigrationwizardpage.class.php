<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__).'/migrationwizardpage.class.php';

/**
 * This form can be used to let the user select the settings
 * 
 * @author Sven Vanpoucke
 */
class SystemMigrationWizardPage extends MigrationWizardPage
{
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('System_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{
		return Translation :: get_lang('System_info') . ':';
	}
	
	/**
	 * Build the form
	 */
	function buildForm()
	{
		$this->_formBuilt = true; 
		$this->addElement('select', 'old_system', Translation :: get_lang('Old_system') . ':', $this->get_old_systems_list());
		$buttons[0] = HTML_QuickForm :: createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($buttons, 'buttons', '', '&nbsp', false);
		$this->setDefaultAction('next');
		$this->set_form_defaults();
	}
	
	function get_old_systems_list()
	{
		//TODO: add function to get more available systems
		$system_list = array();
		$system_list['dokeos185'] = 'dokeos 1.8.5';
		return $system_list;
	}
	
	function set_form_defaults()
	{
		$defaults = array();
		$defaults['old_system'] = 'dokeos185';
		$this->setDefaults($defaults);
	}
}
?>