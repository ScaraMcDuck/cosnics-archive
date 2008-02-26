<?php
/**
 * @package migration.migration_manager
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

//require_once dirname(__FILE__).'/wizard/languageinstallwizardpage.class.php';

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
		$this->addPage(new LanguageInstallWizardPage('page_language',$this->parent));
	}
}
?>