<?php
/**
============================================================================== 
*	@package dokeos.scorm
============================================================================== 
*/
	$langFile = "scormdocument";
	include('../inc/claro_init_global.inc.php');
$this_section=SECTION_COURSES;

	$nameTools = get_lang('ScormBuilder');
	$noPHP_SELF=true;
	$interbredcrump[]= array ("url"=>"./scormdocument.php", "name"=> get_lang('Doc'));
	Display::display_header($nameTools,"Path");
?>