<?php // $Id$ 
/**
============================================================================== 
*	@package dokeos.learnpath
============================================================================== 
*/

api_use_lang_files("learnpath");

include('../inc/claro_init_global.inc.php');
$this_section=SECTION_COURSES;

$source_id = $_REQUEST['source_id'];
$action = $_REQUEST['action'];
$learnpath_id = mysql_real_escape_string($_REQUEST['learnpath_id']);
$chapter_id = $_REQUEST['chapter_id'];
$originalresource = $_REQUEST['originalresource'];

if ( isset($_GET['view_as_role']) && $_GET['view_as_role'] )
{
	$htmlHeadXtra[] =  "<script type='text/javascript'>\n/* <![CDATA[ */\n window.location=\"learnpath_handler.php?source_id=$source_id&action=$action&learnpath_id=$learnpath_id&chapter_id=$chapter_id&originalresource=no\";\n/* ]]> */\n</script>";
}

$noPHP_SELF=true;

$tbl_learnpath_main = $_course['dbNameGlu']."learnpath_main";
$sql="SELECT * FROM `$tbl_learnpath_main` WHERE learnpath_id=$learnpath_id";
$result=api_sql_query($sql,__FILE__,__LINE__);
$therow=mysql_fetch_array($result);

$interbredcrump[]= array ("url"=>"../scorm/scormdocument.php", "name"=> get_lang('_learning_path'));
Display::display_header($therow['learnpath_name'],"Path");
?>