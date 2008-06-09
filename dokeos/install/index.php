<?php
session_start();

$cidReset = true;
$this_section = 'install';

ini_set('include_path',realpath(dirname(__FILE__).'/../plugin/pear'));
ini_set("memory_limit"		,"3500M"	);	// Geen php-beperkingen voor geheugengebruik
ini_set("max_execution_time"	,"72000");	// Twee uur moet voldoende zijn...

require_once dirname(__FILE__).'/../common/filesystem/path.class.php';
require_once Path :: get_library_path().'html/display.class.php';
//require_once(dirname(__FILE__).'/../common/configuration/configuration.class.php');
require_once(Path :: get_library_path().'session/session.class.php');
require_once(Path :: get_library_path().'translation/translation.class.php');
require_once Path :: get_library_path().'html/text.class.php';
require_once Path :: get_library_path().'mail/mail.class.php';
require_once dirname(__FILE__).'/lib/install_manager/install_manager.class.php';

Translation :: set_application($this_section);
Translation :: set_language('english');

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