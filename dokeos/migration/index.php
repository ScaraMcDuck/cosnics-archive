<?php
session_start();

$cidReset = true;
$this_section = 'migration';

<<<<<<< .mine
require_once dirname(__FILE__).'/../common/global.inc.php';
=======
ini_set('include_path',realpath(dirname(__FILE__).'/../plugin/pear'));

require_once dirname(__FILE__).'/../main/inc/installedVersion.inc.php';
>>>>>>> .r14457
require_once dirname(__FILE__).'/lib/migration_manager/migrationmanager.class.php';

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