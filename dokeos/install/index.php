<?php
session_start();

$cidReset = true;
$this_section = 'install';

ini_set('include_path',realpath(dirname(__FILE__).'/../plugin/pear'));

require_once dirname(__FILE__).'/../common/html/display.class.php';
require_once dirname(__FILE__).'/lib/install_manager/installmanager.class.php';
//TODO: Temporary solution
//require_once dirname(__FILE__).'/../common/filesystem/path.class.php';

Translation :: set_application($this_section);
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