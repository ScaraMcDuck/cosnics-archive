<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Jan Bols & Rene Haentjens (UGent)
	
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
 * Dropbox module for Dokeos
 * Initialisation, configuration and functions
 *
 * @author Jan Bols, original design and implementation
 * @author Rene Haentjens, mailing, feedback, folders, user-sortable tables
 * @author Roan Embrechts, virtual course support
 * @author Patrick Cool, config settings, tool introduction and refactoring
 * @package dokeos.dropbox
==============================================================================
*/

$langFile = "dropbox";
require("../inc/claro_init_global.inc.php");

// Roles and rights system
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_DROPBOX);
$is_allowed = RolesRights::is_allowed_which_rights($role_id, $location_id);

//block users without view right
RolesRights::protect_location($role_id, $location_id);

function get_url_param($url_param, $pattern = '^[0-9]+$', $default_value = '')
{
    return isset($_GET[$url_param]) && ereg($pattern, $_GET[$url_param]) ? 
        $_GET[$url_param] : $default_value;
}

$origin = get_url_param('origin', '^learnpath$');

define('URL_OR', '?'.api_get_cidreq() . ($origin ? '&origin=' . $origin : ''));


/*
* When calling dropbox_submit, pass this unique request identifier
* to prevent accidental repeat of dropbox_submit
*/

$dropbox_unid = md5(uniqid(rand(), TRUE));

define('URL_ORUN', URL_OR . '&dropbox_unid=' . urlencode($dropbox_unid));


function dropbox_lang($variable, $notrans = 'DLTT')
{
    return (get_setting('server_type') == 'test' ?
        get_lang('dropbox_lang["'.$variable.'"]', $notrans) :
        str_replace("\\'", "'", $GLOBALS['dropbox_lang'][$variable]));
}

$nameTools = dropbox_lang("dropbox", "noDLTT");


/*
* Libraries & authorization
*/

require_once("dropbox_class.inc.php");
require_once(api_get_library_path() . '/course.lib.php');
require_once(api_get_library_path() . '/groupmanager.lib.php');
require_once(api_get_library_path() . '/tablesort.lib.php');

$is_course_member = CourseManager::is_user_subscribed_in_real_or_linked_course($user_id, $course_id);

api_protect_course_script();

if(!$is_course_member)
{
	api_not_allowed();
	if ($origin != 'learnpath') Display::display_footer();
	exit();
}


/*
* Configuration
*/

$dropbox_cnf["fileTbl"] =   Database::get_course_table('dropbox_file');
$dropbox_cnf["postTbl"] =   Database::get_course_table('dropbox_post');
$dropbox_cnf["personTbl"] = Database::get_course_table('dropbox_person');

$dropbox_cnf["sysPath"] = api_get_path('SYS_COURSE_PATH') . $_course["path"] . "/dropbox"; //path to dropbox subdir in course containing the uploaded files
$dropbox_cnf["maxFilesize"] = api_get_setting("dropbox_max_filesize"); //100 MB file size limit. Other limits maybe imposed in php.ini
$dropbox_cnf["allowOverwrite"] = string_2_boolean(get_setting("dropbox_allow_overwrite"));
$dropbox_cnf["allowJustUpload"] = string_2_boolean(get_setting("dropbox_allow_just_upload"));
$dropbox_cnf["allowStudentToStudent"] = string_2_boolean(get_setting("dropbox_allow_student_to_student"));
$dropbox_cnf["allowGroup"] = string_2_boolean(get_setting("dropbox_allow_group"));

$dropbox_cnf["allowMailing"] = string_2_boolean(get_setting("dropbox_allow_mailing"));  // false = no mailing functionality
$dropbox_cnf["mailingIdBase"] = 10000000;  // bigger than any user_id,
// allowing enough space for pseudo_ids as uploader_id, dest_user_id, user_id:
// mailing pseudo_id = dropbox_cnf("mailingIdBase") + mailing id
$dropbox_cnf["mailingZipRegexp"] = '/^(.*)(STUDENTID|USERID|LOGINNAME)(.*)\.ZIP$/i';
$dropbox_cnf["mailingWhereSTUDENTID"] = "official_code";
$dropbox_cnf["mailingWhereUSERID"] = "username";
$dropbox_cnf["mailingWhereLOGINNAME"] = "username";
$dropbox_cnf["mailingFileRegexp"] = '/^(.+)\.\w{1,4}$/';


/*
* More functions
*/

function dropbox_cnf($variable)
{
    return $GLOBALS['dropbox_cnf'][$variable];
}

function getUserNameFromId ( $id)  // Mailing: return 'Mailing ' + id
{
    $mailingId = $id - dropbox_cnf("mailingIdBase");
    if ( $mailingId > 0) return dropbox_lang("mailingAsUsername", "noDLTT") . ' ' . $mailingId;

    $user_table = Database::get_main_table(MAIN_USER_TABLE);
    $sql = "SELECT CONCAT(lastname,' ', firstname) AS name
			FROM $user_table
			WHERE user_id='" . addslashes( $id) . "'";
    $result = api_sql_query($sql,__FILE__,__LINE__);
    $res = mysql_fetch_array( $result);

    if ( $res == FALSE) return FALSE;
    return stripslashes( $res["name"]);
}

function getLoginFromId ( $id)
{
    $user_table = Database::get_main_table(MAIN_USER_TABLE);
    $sql = "SELECT username
			FROM $user_table
			WHERE user_id='" . addslashes( $id) . "'";
    $result =api_sql_query($sql,__FILE__,__LINE__);
    $res = mysql_fetch_array( $result);
    if ( $res == FALSE) return FALSE;
    return stripslashes( $res["username"]);
}

function nbs($s)
{
    return str_replace(' ', '&nbsp;', htmlspecialchars($s));
}

function dbla($dbls)
{
    return dropbox_lang($dbls, 'noDLTT');
}

function dropbox_link($php, $url_params, $inner_html)
{
    return '<a href="'.$php.'.php'.$url_params.'">' . $inner_html . '</a>';
}

function dbs_link($img, $img_title = '', $dbs_action = '', $on_click = '')
{
    return dropbox_link('dropbox_submit', URL_ORUN . $dbs_action . 
            ($on_click ? '" onClick="' . $on_click : ''), 
        '<img src="../img/' . $img . '.gif" border="0' . 
        ($img_title ? '" title="' . $img_title .  '" alt="' . $img_title : '') . 
        '" />');
}

function dbv_sel($v, $k, $p = '', $s = FALSE)
{
    return '<option value="'.$k.'.php'.URL_OR.$p.($s ? '" selected>' : '">').$v.'</option>';

}

function dbv($folders, $cf, $back = FALSE)  // dbv($dropbox_person->folders);
{
    echo '<br>', dropbox_lang('filingFolders'), ':&nbsp;', 
        '<select onchange="document.location.href = this.options[this.selectedIndex].value;">', 
        ($back ? '' : dbv_sel('-', 'index')), 
        dbv_sel(dropbox_lang('receivedTitle'), 'dropbox_folder', '&fid=-2', $cf == -2), 
        dbv_sel(dropbox_lang('sentTitle'), 'dropbox_folder', '&fid=-1', $cf == -1);
    foreach ($folders as $fi => $f) 
        echo dbv_sel(htmlspecialchars($f), 'dropbox_folder', '&fid=' . $fi, $cf == $fi);
    if ($back) echo dbv_sel(dropbox_lang("mailingBackToDropbox"), 'index');
    echo '</select>';
}

function dfh($dfh_title, $dfh_action, $stuff_1 = '', $stuff_2 = '', $stuff_3 = '')
{
    echo '<div class="dropbox_listTitle" style="text-align: right">', 
        $stuff_1, '<b>', $dfh_title, '</b>', $stuff_2, 
        dbs_link('delete', get_lang('Delete') . ': ' . $dfh_title, $dfh_action, 
	        "return confirmation('" . addslashes(dbla('all')) . "');"), 
	    $stuff_3, "</div>\n";
}
?>