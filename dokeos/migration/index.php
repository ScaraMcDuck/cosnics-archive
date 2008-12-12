<?php
/**
 * Start webinterface
 */
$cidReset = true;
$this_section = 'migration';

ini_set("memory_limit"		,"3500M"	);	// Geen php-beperkingen voor geheugengebruik
	ini_set("max_execution_time"	,"72000");	// Twee uur moet voldoende zijn...

require_once dirname(__FILE__).'/../common/global.inc.php';
require_once dirname(__FILE__).'/lib/migration_manager/migration_manager.class.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';

$language_interface = 'english';

Translation :: set_application($this_section);

/**if (!Authentication :: is_valid())
{
	Display :: not_allowed();
}*/

//$usermgr = new UserManager(Session :: get_user_id());
//$user = $usermgr->retrieve_user(Session :: get_user_id());

$migmgr = new MigrationManager($user);
try
{
	$migmgr->run();
}
catch(Exception $exception)
{
	$repmgr->display_header();
	Display :: error_message($exception->getMessage());
	$repmgr->display_footer();
}
?>