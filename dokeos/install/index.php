<?php
$cidReset = true;
$this_section = 'install';
require_once dirname(__FILE__).'/../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/lib/install_manager/installmanager.class.php';
require_once dirname(__FILE__).'/../users/lib/usermanager/usermanager.class.php';

api_use_lang_files('install');

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