<?php

/**
 * @package migration.migrationmanager
 */
 
require_once dirname(__FILE__).'/../migration_manager.class.php';
require_once dirname(__FILE__).'/../migration_manager_component.class.php';
require_once dirname(__FILE__).'/inc/migration_wizard.class.php';

/**
 * Migration MigrationManagerComponent which allows the administrator to migrate to LCMS
 *
 * @author Sven Vanpoucke
 */
class MigrationManagerMigrationComponent extends MigrationManagerComponent 
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$wizard = new MigrationWizard($this);
		$wizard->run();
	}	
}
?>