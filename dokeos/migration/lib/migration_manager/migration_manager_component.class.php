<?php

require_once Path :: get_library_path() . 'core_application_component.class.php';

/**
 * @package migration.migrationmanager
 * 
 * A MigrationManagerComponent is an abstract class that represents a component that is used
 * in the migrationmanager
 *
 * @author Sven Vanpoucke
 */
abstract class MigrationManagerComponent extends CoreApplicationComponent 
{
	protected function MigrationManagerComponent($migration_manager) 
	{
		parent :: __construct($migration_manager);
	}
}
?>