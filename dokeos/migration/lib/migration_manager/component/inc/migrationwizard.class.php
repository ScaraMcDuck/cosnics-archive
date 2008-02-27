<?php
/**
 * @package migration.lib.migration_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__).'/wizard/systemmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/settingsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/migrationwizarddisplay.class.php';
require_once dirname(__FILE__).'/wizard/migrationwizardprocess.class.php';

/**
 * A wizard which guides the user through several steps to perform the migration
 * 
 * @author Sven Vanpoucke
 */
class MigrationWizard extends HTML_QuickForm_Controller
{
	/** 
	 * The component in which the wizard runs
	 */
	private $parent;
	/**
	 * Creates a new MigrationWizard
	 * @param MigrationManagerComponent $parent The migrationmanager component 
	 * in which this wizard runs.
	 */
	function MigrationWizard($parent)
	{
		global $language_interface;
		$this->parent = $parent;
		parent :: HTML_QuickForm_Controller('MigrationWizard', true);
		$this->addPage(new SystemMigrationWizardPage('page_system',$this->parent));
		$this->addPage(new SettingsMigrationWizardPage('page_settings',$this->parent));
		$this->addAction('display', new MigrationWizardDisplay($this->parent));
		$this->addAction('process', new MigrationWizardProcess($this->parent));
	}
}
?>