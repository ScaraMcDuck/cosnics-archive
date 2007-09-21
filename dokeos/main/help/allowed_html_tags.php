<?php

//$Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Bart Mollet, Hogeschool Gent

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	This	 script displays a help window with an overview of the allowed HTML-
*   tags  and their attributes.
*
*	@package dokeos.help
==============================================================================
*/
require_once ('../inc/claro_init_global.inc.php');
api_use_lang_files('help');
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once (api_get_library_path().'/formvalidator/Rule/HTML.php');
$language_code = Database :: get_language_isocode($language_interface);
header('Content-Type: text/html; charset='.$charset);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language_code; ?>" lang="<?php echo $language_code; ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
<title>
<?php echo get_lang('AllowedHTMLTags'); ?>
</title>
<link rel="stylesheet" type="text/css" href="<?php echo api_get_path(WEB_CODE_PATH); ?>css/default.css"/>
</head>
<body>
<div style="margin:10px;">
<div style="text-align:right;"><a href="javascript:window.close();"><?php echo get_lang('Close'); ?></a></div>
<h4>
<?php echo get_lang('AllowedHTMLTags'); ?>
</h4>
<?php

$html_type = $_SESSION['status'] == COURSEMANAGER ? TEACHER_HTML : STUDENT_HTML;
$fullpage = $_GET['fullpage'] == '0' ? false : true;
$tags = HTML_QuickForm_Rule_HTML :: get_allowed_tags($html_type,$fullpage);
$table_header = array();
$table_header[]= array('tag',true);
$table_header[]= array('attributes',false);
foreach ($tags as $tag => $attributes)
{
	$row = array();
	$row[] = '<kbd>'.$tag.'</kbd>';
	$row[] = '<kbd>&nbsp;'.implode(', ',array_keys($attributes)).'</kbd>';
	$table_data[] = $row;
}
Display::display_sortable_table($table_header,$table_data,array(),array(),array('fullpage'=>$_GET['fullpage']));
?>
<div style="text-align:right;"><a href="javascript:window.close();"><?php echo get_lang('Close'); ?></a></div>
</div>
</body>
</html>