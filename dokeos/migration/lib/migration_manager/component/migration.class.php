<?php

/**
 * @package migration.migrationmanager
 */
 
require_once dirname(__FILE__).'/../migrationmanager.class.php';
require_once dirname(__FILE__).'/../migrationmanagercomponent.class.php';
require_once dirname(__FILE__).'/inc/migrationwizard.class.php';

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