<?php // $id: $
/**
============================================================================== 
*	@package dokeos.scorm
============================================================================== 
*/
	api_use_lang_files("scormdocument");

	include('../inc/claro_init_global.inc.php');
$this_section=SECTION_COURSES;

	$openDir = $_GET['openDir'];
	$pos=strrpos($openDir,'//');
	$nameTools = substr($openDir,$pos+1,strlen($openDir));

	$noPHP_SELF=true;

	$interbredcrump[]= array ("url"=>"./scormdocument.php", "name"=> get_lang('Doc'));
	Display::display_header($nameTools,"Path");
?>