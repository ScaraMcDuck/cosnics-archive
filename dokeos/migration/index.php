<?php
session_start();

$cidReset = true;
$this_section = 'migration';

require_once dirname(__FILE__).'/../main/inc/installedVersion.inc.php';
require_once dirname(__FILE__).'/lib/migration_manager/migrationmanager.class.php';
require_once dirname(__FILE__).'/../main/inc/global.inc.php';

$language_interface = 'english';

Translation :: set_application($this_section);

$repmgr = new MigrationManager();
try
{
	$repmgr->run();
}
catch(Exception $exception)
{
	$repmgr->display_header();
	Display::display_error_message($exception->getMessage());
	$repmgr->display_footer();
}
?>