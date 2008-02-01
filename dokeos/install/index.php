<?php
session_start();

$cidReset = true;
$this_section = 'install';

$GLOBALS['clarolineRepositorySys'] = dirname(__FILE__) . '/../main/';

require_once dirname(__FILE__).'/../main/inc/lib/main_api.lib.php';
ini_set('include_path',realpath(dirname(__FILE__).'/../plugin/pear'));

require_once dirname(__FILE__).'/../main/inc/installedVersion.inc.php';
require_once dirname(__FILE__).'/../main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/../main/inc/lib/database.lib.php';
require_once dirname(__FILE__).'/../main/inc/lib/display.lib.php';
require_once dirname(__FILE__).'/lib/install_manager/installmanager.class.php';

api_use_lang_files('trad4all', 'install');
$language_interface = 'english';

$repmgr = new InstallManager(null);
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