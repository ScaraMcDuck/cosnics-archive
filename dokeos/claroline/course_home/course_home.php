<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University
	Copyright (c) 2001 Universite Catholique de Louvain
	Copyright (c) various contributors

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
*                  HOME PAGE FOR EACH COURSE
*
*	This page, included in every course's index.php is the home
*	page. To make administration simple, the teacher edits his
*	course from the home page. Only the login detects that the
*	visitor is allowed to activate, deactivate home page links,
*	access to the teachers tools (statistics, edit forums...).
*
* Edit visibility of tools
*
*     visibility = 1 - everybody
*     visibility = 0 - course admin (teacher) and platform admin
*     visibility = 2 - platform admin
*
* Who can change visibility ?
*
*     admin = 0 - course admin (teacher) and platform admin
*     admin = 1 - platform admin
*
* Show message to confirm that a tools must be hide from available tools
*
*     visibility 0,1->2 - $remove
*
* Deleting tools or hiding them from the list of available tools.
*
*     visibility = 2 are only displayed to platform admin
*     visibility 0,1->2 - $destroy
*
*     visibility 1 -> 0 - $hide / $restore
*
*	@package dokeos.course_home
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/

if (!isset ($cidReq))
{
	$cidReq = $dbname; // to provide compatibility. with previous  system

	GLOBAL $error_msg, $error_no;

	$classError = "init";
	$error_no[$classError][] = "2";
	$error_level[$classError][] = "info";
	$error_msg[$classError][] = "[".__FILE__."][".__LINE__."] cidReq was Missing $cidReq take $dbname;";

}
$section = "course";
$langFile = "course_home";

include ('../../claroline/inc/claro_init_global.inc.php');
$this_section = SECTION_COURSES;

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
include_once (api_get_library_path()."/course.lib.php");
include_once (api_get_library_path()."/debug.lib.inc.php");
include_once (api_get_library_path()."/text.lib.php");

/*
-----------------------------------------------------------
	Constants
-----------------------------------------------------------
*/
define("TOOL_PUBLIC", "Public");
define("TOOL_PUBLIC_BUT_HIDDEN", "PublicButHide");
define("TOOL_COURSE_ADMIN", "courseAdmin");
define("TOOL_PLATFORM_ADMIN", "platformAdmin");

/*
-----------------------------------------------------------
	Virtual course support code
-----------------------------------------------------------
*/
$user_id = api_get_user_id();
$course_code = $_course["sysCode"];
$course_info = Database :: get_course_info_from_code($course_code);

$return_result = CourseManager :: determine_course_title_from_course_info($_uid, $course_info);
$course_title = $return_result["title"];
$course_code = $return_result["code"];

$_course["name"] = $course_title;
$_course['official_code'] = $course_code;

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/

$nameTools = $course_title;
Display :: display_header($nameTools, "Home");

$is_allowed_to_edit = api_is_allowed_to_edit();

// Roles and rights system
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights :: get_local_user_role_id($user_id, $course_id);
/*
	For now, we will use the course right values to determine the rights for this page.
	In a way, this page _is_ the visible part of the course location.
	Otherwise, strange behaviour ensues: what happens if users can access the course but not the
	course homepage?
*/
//$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_COURSE_HOMEPAGE);
$location_id = RolesRights :: get_course_location_id($course_id);
$is_allowed = RolesRights :: is_allowed_which_rights($role_id, $location_id);

//block users without view right
RolesRights :: protect_location($role_id, $location_id);

// Stats :
// Count only one time by course and by session
if (!isset ($coursesAlreadyVisited[$_cid]))
{
	include (api_get_library_path()."/events.lib.inc.php");
	event_access_course();
	$coursesAlreadyVisited[$_cid] = 1;
	api_session_register('coursesAlreadyVisited');
}

$tool_table = Database :: get_course_tool_list_table();

$temps = time();
$reqdate = "&reqdate=$temps";

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
* The list of standard course tools to show comes from the course library.
* There are two special cases taken care of inside this function: learning paths
* and links, both can be added to the course homepage but these extra entries
* are not in the roles-rights system yet.
*/
function show_tools($course_tool_category, $tool_list, $is_allowed)
{
	$web_code_path = api_get_path(WEB_CODE_PATH);
	$course_link_table = Database :: get_course_table(LINK_TABLE);
	$course_item_property_table = Database :: get_course_table(ITEM_PROPERTY_TABLE);
	$course_tool_table = Database::get_course_tool_list_table();
		
	// grabbing all the links that have the property on_homepage set to 1
	// and all the learning paths
	switch ($course_tool_category)
	{
		case TOOL_PUBLIC :
			$sql_links = "SELECT tl.*, tip.visibility
								FROM $course_link_table tl
								LEFT JOIN $course_item_property_table tip ON tip.tool='link' AND tip.ref=tl.id
								WHERE tl.on_homepage='1' AND tip.visibility = 1";
			$learning_path_sql = "	SELECT * FROM $course_tool_table
									WHERE admin=0 AND visibility = 1 AND link LIKE 'learnpath/learnpath_handler.php?%' ORDER BY id";
			break;
		case TOOL_PUBLIC_BUT_HIDDEN :
			$sql_links = "SELECT tl.*, tip.visibility
							FROM $course_link_table tl
							LEFT JOIN $course_item_property_table tip ON tip.tool='link' AND tip.ref=tl.id
							WHERE tl.on_homepage='1' AND tip.visibility = 0";
			$learning_path_sql = null;
			break;
		default :
			$sql_links = null;
			$learning_path_sql = null;
			break;
	}

	if ($sql_links != null)
	{
		$result_links = api_sql_query($sql_links, __FILE__, __LINE__);
		while ($links_row = mysql_fetch_array($result_links))
		{
			$properties = array ();
			$properties['name'] = $links_row['title'];
			$properties['link'] = $links_row['url'];
			$properties['visibility'] = $links_row['visibility'];
			$properties['image'] = ($course_tool_category == TOOL_PUBLIC_BUT_HIDDEN) ? "external_na.gif" : "external.gif";
			$properties['adminlink'] = api_get_code_web_path()."link/link.php?action=editlink&id=".$links_row['id'];
			$tool_list[] = $properties;
		}
	}
	
	if ($learning_path_sql != null)
	{
		$learning_path_result = api_sql_query($learning_path_sql, __FILE__, __LINE__);
		while ($learning_path_row = mysql_fetch_array($learning_path_result))
		{
			$tool_list[] = $learning_path_row;
		}
	}

	$i = 0;
	foreach ($tool_list as $toolsRow)
	{
		if (!($i % 2))
		{
			echo "<tr valign=\"top\">\n";
		}

		// NOTE : table contains only the image file name, not full path
		if (!stristr($toolsRow['link'], 'http://') && !stristr($toolsRow['link'], 'https://') && !stristr($toolsRow['link'], 'ftp://'))
		{
			$toolsRow['link'] = $web_code_path.$toolsRow['link'];
		}
		if ($course_tool_category == TOOL_PUBLIC_BUT_HIDDEN)
		{
			$class = "class=\"invisible\"";
			if ($toolsRow['image'] != 'scormbuilder.gif')
			{
				$toolsRow['image'] = str_replace('.gif', '_na.gif', $toolsRow['image']);
			}
		}
		$qm_or_amp = ((strpos($toolsRow['link'], '?') === FALSE) ? '?' : '&amp;');
		echo '<td width="50%" height="30">', "\n", '<a href="', htmlspecialchars($toolsRow['link']). (($toolsRow['image'] == "external.gif" || $toolsRow['image'] == "external_na.gif") ? '' : $qm_or_amp.api_get_cidreq()), '" target="', $toolsRow['target'], '" '.$class.'>', '<img src="', $web_code_path, 'img/', $toolsRow['image'], '" style="vertical-align: middle" border="0" alt="', $toolsRow['image'], '" />', '&nbsp;', ($toolsRow['image'] == "external.gif" || $toolsRow['image'] == "external_na.gif" || $toolsRow['image'] == "scormbuilder.gif") ? htmlspecialchars($toolsRow['name']) : get_lang($toolsRow['name']), "</a>\n ";

		// This part displays the links to hide or remove a tool.
		// These links are only visible by the course manager.
		$lnk = array ();
		if ($is_allowed[EDIT_RIGHT])
		{
			if ($course_tool_category == TOOL_PUBLIC && !strpos($toolsRow['link'], 'learnpath_handler.php?learnpath_id'))
			{
				$link['name'] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/visible.gif" style="vertical-align: middle" alt="'.get_lang("Deactivate").'"/>';
				$link['cmd'] = "hide=yes";
				$lnk[] = $link;
			}

			if ($course_tool_category == TOOL_PUBLIC_BUT_HIDDEN)
			{
				$link['name'] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/invisible.gif" style="vertical-align: middle" alt="'.get_lang("Activate").'"/>';
				$link['cmd'] = "restore=yes";
				$lnk[] = $link;

				if ($toolsRow["added_tool"] == 1)
				{
					$link['name'] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif" style="vertical-align: middle" alt="'.get_lang("Remove").'"/>';
					$link['cmd'] = "remove=yes";
					$lnk[] = $link;
				}
			}
			if ($toolsRow['adminlink'])
			{
				echo '<a href="'.$toolsRow['adminlink'].'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif" style="vertical-align: middle" alt="'.get_lang("Edit").'"/></a>';
				//echo "edit link:".$properties['adminlink'];
			}

		}
		if (api_is_platform_admin())
		{
			if ($toolsRow["visibility"] == 2)
			{
				$link['name'] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/undelete.gif" style="vertical-align: middle" alt="'.get_lang("Activate").'"/>';

				$link['cmd'] = "hide=yes";
				$lnk[] = $link;

				if ($toolsRow["added_tool"] == 1)
				{
					$link['name'] = get_lang("Delete");
					$link['cmd'] = "askDelete=yes";
					$lnk[] = $link;
				}
			}

			if ($course_tool_category == TOOL_PUBLIC_BUT_HIDDEN && $toolsRow["added_tool"] == 0 && $is_allowed[DELETE_RIGHT])
			{
				$link['name'] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif" style="vertical-align: middle" alt="'.get_lang("Remove").'"/>';
				$link['cmd'] = "remove=yes";
				$lnk[] = $link;
			}
		}
		if (is_array($lnk))
		{
			foreach ($lnk as $this_link)
			{
				if (!$toolsRow['adminlink'])
				{
					echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&amp;id=".$toolsRow["id"]."&amp;".$this_link['cmd']."\">".$this_link['name']."</a>";
				}
			}
		}

		// Allow editing of invisible homepage links (modified external_module)
		if ($toolsRow["added_tool"] == 1 && $is_allowed[EDIT_RIGHT] && !$toolsRow["visibility"])
			echo "<a class=\"nobold\" href=\"".api_get_path(WEB_PATH).'claroline/external_module/external_module.php'."?".api_get_cidreq()."&amp;id=".$toolsRow["id"]."\">".get_lang("Edit")."</a>";

		echo "</td>\n";

		if ($i % 2)
		{
			echo "</tr>\n";
		}

		$i ++;
	}

	if ($i % 2)
	{
		echo "<td width=\"50%\">&nbsp;</td>\n", "</tr>\n";
	}
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/

/*
-----------------------------------------------------------
	SWITCH TO A DIFFERENT HOMEPAGE VIEW
	the setting homepage_view is adjustable through
	the platform administration section
-----------------------------------------------------------
*/
//display course title for course home page (similar to toolname for tool pages)
api_display_tool_title($nameTools);

//introduction section
$moduleId = TOOL_COURSE_HOMEPAGE;
Display :: display_introduction_section(TOOL_COURSE_HOMEPAGE, $is_allowed);

/*
-----------------------------------------------------------
	Work with data post askable by admin of course (franglais, clean this)
-----------------------------------------------------------
*/
if ($is_allowed[EDIT_RIGHT])
{
	/*  Work request */

	/*
	-----------------------------------------------------------
		Modify home page
	-----------------------------------------------------------
	*/

	/*
	 * display message to confirm that a tool must be hidden from the list of available tools
	 * (visibility 0,1->2)
	 */

	if ($_GET["remove"])
	{
		$msgDestroy = get_lang('DelLk').'<br />';
		$msgDestroy .= '<a href="'.$_SERVER['PHP_SELF'].'">'.get_lang('No').'</a>&nbsp;|&nbsp;';
		$msgDestroy .= '<a href="'.$_SERVER['PHP_SELF'].'?destroy=yes&amp;id='.$_GET["id"].'">'.get_lang('Yes').'</a>';

		Display :: display_normal_message($msgDestroy);
	}

	/*
	 * Process hiding a tools from available tools.
	 * visibility=2 are only view  by Dokeos Administrator (visibility 0,1->2)
	 */

	elseif ($_GET["destroy"])
	{
		api_sql_query("UPDATE $tool_table SET visibility='2' WHERE id='".$_GET["id"]."'", __FILE__, __LINE__);
	}

	/*
	-----------------------------------------------------------
		HIDE
	-----------------------------------------------------------
	*/
	elseif ($_GET["hide"]) // visibility 1 -> 0
	{
		$tool_id = $_GET["id"];
		api_sql_query("UPDATE $tool_table SET visibility=0 WHERE id='$tool_id'", __FILE__, __LINE__);
		CourseManager :: set_course_tool_visibility($tool_id, false);
		Display :: display_normal_message(get_lang('ToolIsNowHidden'));
	}

	/*
	-----------------------------------------------------------
		REACTIVATE
	-----------------------------------------------------------
	*/
	elseif ($_GET["restore"]) // visibility 0,2 -> 1
	{
		$tool_id = $_GET["id"];
		api_sql_query("UPDATE $tool_table SET visibility=1  WHERE id='$tool_id'", __FILE__, __LINE__);
		CourseManager :: set_course_tool_visibility($tool_id, true);
		Display :: display_normal_message(get_lang('ToolIsNowVisible'));
	}
}

// work with data post askable by admin of course

if ($is_allowed[EDIT_RIGHT])
{
	// Show message to confirm that a tools must be hide from available tools
	// visibility 0,1->2
	if ($_GET["askDelete"])
	{
?>
			<div id="toolhide">
			<?php echo get_lang("DelLk"); ?>
			<br />&nbsp;&nbsp;&nbsp;
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>"><?php echo get_lang("No"); ?></a>&nbsp;|&nbsp;
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=yes&id=<?php echo $_GET["id"]; ?>"><?php echo get_lang("Yes"); ?></a>
			</div>
		<?php

	}

	/*
	 * Process hiding a tools from available tools.
	 * visibility=2 are only view  by Dokeos Administrator visibility 0,1->2
	 */

	elseif (isset ($_GET["delete"]) && $_GET["delete"])
	{
		api_sql_query("DELETE FROM $tool_table WHERE id='$id' AND added_tool=1", __FILE__, __LINE__);
	}
}

/*
==============================================================================
		TOOLS VISIBLE FOR EVERYBODY
==============================================================================
*/

if (api_get_setting('homepage_view') != "default")
{
	include (api_get_setting('homepage_view').'.php');
}
else
{
	echo "<div class=\"everybodyview\">";
	echo "<table width=\"100%\">";

	$tool_list = CourseManager :: get_accessible_public_tools($course_id, $role_id);
	show_tools(TOOL_PUBLIC, $tool_list, $is_allowed);

	echo "</table>";
	echo "</div>";

	/*
	==============================================================================
			COURSE ADMIN ONLY VIEW
	==============================================================================
	*/

	$view_as_role = $_SESSION['view_as_role'];
	if (isset($view_as_role) && $view_as_role) $role_id = $view_as_role;
	if ($role_id == TEACHING_ASSISTANT || $role_id == COURSE_ADMIN)
	{
		// start of tools for CourseAdmins (teachers/tutors)
		$course_admin_tool_list = CourseManager :: get_course_admin_section_tools($course_id, $role_id);
		$inactive_tool_list = CourseManager :: get_hidden_public_tools($course_id, $role_id);
		if (count($course_admin_tool_list) > 0 || count($inactive_tool_list) > 0)
		{
			echo "<div class=\"courseadminview\">";
			echo "<span class=\"viewcaption\">";
			echo get_lang("CourseAdminOnly");
			echo "</span>";
			echo "<table width=\"100%\">";
	
			show_tools(TOOL_COURSE_ADMIN, $course_admin_tool_list, $is_allowed);
	
			/*
			-----------------------------------------------------------
				INACTIVE TOOLS - HIDDEN (GREY) LINKS
			-----------------------------------------------------------
			*/
	
			echo "<tr><td colspan=\"4\"><hr style='color:\"#4171B5\"' noshade=\"noshade\" size=\"1\" /></td></tr>\n", "<tr>\n", "<td colspan=\"4\">\n", "<div style=\"margin-bottom: 10px;\"><font color=\"#808080\">\n", get_lang("InLnk"), "</font></div>", "</td>\n", "</tr>\n";
	
			show_tools(TOOL_PUBLIC_BUT_HIDDEN, $inactive_tool_list, $is_allowed);
		
			echo "</table>";
			echo "</div> ";
		}
	}

	/*
	-----------------------------------------------------------
		Tools for platform admin only
	-----------------------------------------------------------
	*/

	if (api_is_platform_admin() && $is_allowed[EDIT_RIGHT])
	{
?>
		<div class="platformadminview">
		<span class="viewcaption"><?php echo get_lang("PlatformAdminOnly"); ?></span>
		<table width="100%">
		<?php

		$tool_list = CourseManager :: get_platform_admin_section_tools($course_id, $role_id);
		show_tools(TOOL_PLATFORM_ADMIN, $tool_list, $is_allowed);
?>
		</table>
		</div>
	<?php

	}
}
/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
?>