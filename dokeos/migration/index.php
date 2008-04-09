<?php
/**
 * Start webinterface
 */

$cidReset = true;
$this_section = 'migration';

ini_set("memory_limit"		,"3500M"	);	// Geen php-beperkingen voor geheugengebruik
	ini_set("max_execution_time"	,"72000");	// Twee uur moet voldoende zijn...

require_once dirname(__FILE__).'/../common/global.inc.php';
require_once dirname(__FILE__).'/lib/migration_manager/migrationmanager.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';

$language_interface = 'english';

Translation :: set_application($this_section);

/**if (!PlatformSession :: get_user_id())
{
	Display :: display_not_allowed();
}*/

//$usermgr = new UserManager(PlatformSession :: get_user_id());
//$user = $usermgr->retrieve_user(PlatformSession :: get_user_id());

$migmgr = new MigrationManager($user);
try
{
	$migmgr->run();
}
catch(Exception $exception)
{
	$repmgr->display_header();
	Display::display_error_message($exception->getMessage());
	$repmgr->display_footer();
}
?>