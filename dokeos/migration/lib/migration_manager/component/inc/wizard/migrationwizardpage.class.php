<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 * 
 * This abstract class defines a page which is used in a migration wizard.
 */
abstract class MigrationWizardPage extends HTML_QuickForm_Page
{
	/**
	 * The MigrationManager component in which the wizard runs.
	 */
	private $parent;
	
	/**
	 * Constructor
	 * @param string $name A unique name of this page in the wizard
	 * @param MigrationManagerComponent $parent The MigrationManager component
	 * in which the wizard runs.
	 */
	public function MigrationWizardPage($name,$parent)
	{
		$this->parent = $parent;
		parent::HTML_QuickForm_Page($name,'post');
	}
	
	/**
	 * Returns the MigrationManager component in which this wizard runs
	 * @return MigrationManager
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	/**
	 * Set the language interface of the wizard page
	 * @param string $lang A name of a language 
	 */
	function set_lang($lang)
	{
		global $language_interface;
		$language_interface = $lang;
	}
	
	function perform()
	{
		
	}
	
	function next_step_info()
	{
		
	}
}

?>