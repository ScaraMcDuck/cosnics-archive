<?php
session_start();

$cidReset = true;
$this_section = 'migration';

$GLOBALS['clarolineRepositorySys'] = dirname(__FILE__) . '/../main/';

require_once dirname(__FILE__).'/../main/inc/lib/main_api.lib.php';
ini_set('include_path',realpath(dirname(__FILE__).'/../plugin/pear'));

require_once dirname(__FILE__).'/../main/inc/installedVersion.inc.php';
require_once dirname(__FILE__).'/../main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/../main/inc/lib/database.lib.php';
require_once dirname(__FILE__).'/../main/inc/lib/display.lib.php';
require_once dirname(__FILE__).'/lib/migration_manager/migrationmanager.class.php';
require_once dirname(__FILE__).'/../common/filesystem/path.class.php';
require_once dirname(__FILE__).'/../common/translation/translation.class.php';

$language_interface = 'english';

Translation :: get_instance()->set_application($this_section);

/*if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

if (!$user->is_platform_admin())
{
	api_not_allowed();
}*/

api_use_lang_files('trad4all', 'migration');

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