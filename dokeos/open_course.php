<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
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
* This script displays a list of courses organised in categories.
* Anonymous users (non-logged in users fro all around the world) see
* the list of courses open for the world (for anonymous guests)
* Logged-in users see the list of courses open for the platform (for registered guests)
*
* @todo
* @package dokeos.main
==============================================================================
*/
/*
==============================================================================
	   INIT SECTION
==============================================================================
*/
// only this script should have this constant defined
define('DOKEOS_HOMEPAGE', true);
// Don't change these settings
define('SCRIPTVAL_No', 0);
define('SCRIPTVAL_InCourseList', 1);
define('SCRIPTVAL_UnderCourseList', 2);
define('SCRIPTVAL_Both', 3);
// End 'don't change' section

api_use_lang_files('courses', 'index');
$cidReset = true; /* Flag forcing the 'current course' reset,
                   as we're not inside a course anymore  */
/*
-----------------------------------------------------------
	Included libraries
-----------------------------------------------------------
*/
//this includes main_api too:
include_once ('./main/inc/claro_init_global.inc.php');
//$this_section = SECTION_COURSES;

include_once (api_get_path(LIBRARY_PATH).'course.lib.php');
include_once (api_get_path(LIBRARY_PATH).'debug.lib.inc.php');
include_once (api_get_path(LIBRARY_PATH).'events.lib.inc.php');
include_once (api_get_path(LIBRARY_PATH).'system_announcements.lib.php');
include_once (api_get_path(LIBRARY_PATH).'text.lib.php');
include_once (api_get_path(LIBRARY_PATH).'groupmanager.lib.php');
include_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
if ($_GET['logout'])
{
	$query_string='';

	if(!empty($_SESSION['user_language_choice']))
	{
		$query_string='?language='.$_SESSION['user_language_choice'];
	}

	LoginDelete($uid, $statsDbName);
	api_session_destroy();

	header("Location: index.php$query_string");
	exit();
}
/*
-----------------------------------------------------------
	Table definitions
-----------------------------------------------------------
*/
//new table definitions, using database library
//these already have backticks around them!
$main_course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
$main_category_table = Database :: get_main_table(MAIN_CATEGORY_TABLE);
$track_login_table = Database :: get_statistic_table(STATISTIC_TRACK_E_LOGIN_TABLE);
/*
-----------------------------------------------------------
	Constants and CONFIGURATION parameters
-----------------------------------------------------------
*/
// ---- Category list options ----
/** defines wether or not anonymous visitors can see a list of the courses on
the Dokeos homepage that are open to the world */
define('DISPLAY_COURSES_TO_ANONYMOUS_USERS', true);
define('CONFVAL_showNodeEmpty', true);
define('CONFVAL_showNumberOfChild', false); // actually count are only for direct children
define('CONFVAL_ShowLinkBackToTopOfTree', false);
// ---- Course list options ----
define('CONFVAL_showCourseLangIfNotSameThatPlatform', true);
// Order to sort data
$orderKey = array('keyTools', 'keyTime', 'keyCourse'); // default "best" Choice
//$orderKey = array('keyTools', 'keyCourse', 'keyTime');
//$orderKey = array('keyCourse', 'keyTime', 'keyTools');
//$orderKey = array('keyCourse', 'keyTools', 'keyTime');
define('CONFVAL_showExtractInfo', SCRIPTVAL_UnderCourseList);
// SCRIPTVAL_InCourseList    // best choice if $orderKey[0] == 'keyCourse'
// SCRIPTVAL_UnderCourseList // best choice
// SCRIPTVAL_Both // probably only for debug
if (isset($_uid))
{
	$nameTools = get_lang("ViewOpenCourses");
}

/*
-----------------------------------------------------------
	Check configuration parameters integrity
-----------------------------------------------------------
*/
if (CONFVAL_showExtractInfo != SCRIPTVAL_UnderCourseList and $orderKey[0] != "keyCourse")
{
	// CONFVAL_showExtractInfo must be SCRIPTVAL_UnderCourseList to accept $orderKey[0] !="keyCourse"
	if (DEBUG || api_is_platform_admin()) // Show bug if admin. Else force a new order
		die("
					<strong>
					config error:".__FILE__."</strong>
					<br/>
					set
					<ul>
						<li>
							CONFVAL_showExtractInfo=SCRIPTVAL_UnderCourseList
							(actually : ".CONFVAL_showExtractInfo.")
						</li>
					</ul>
					or
					<ul>
						<li>
							\$orderKey[0] !=\"keyCourse\"
							(actually : ".$orderKey[0].")
						</li>
					</ul>");
	else
	{
		$orderKey = array ("keyCourse", "keyTools", "keyTime");
	}
}
/*
==============================================================================
		LOGIN
==============================================================================
*/
if ($_GET["submitAuth"] == 1)
{
	echo "Attempted breakin - sysadmins notified.";
	session_destroy();
	die();
}
if ($_POST["submitAuth"])
{
	// To ensure legacy compatibility, we set the following variables.
	// But they should be removed at last.
	$uid = $_uid;
	$lastname		 = $_user['lastName'];
	$firstname	 = $_user['firstName'];
	$email			 = $_user['mail'];
	$status			 = $uData['status'];
	if (isset ($_uid))
	{
		$sqlLastLogin = "SELECT UNIX_TIMESTAMP(login_date)
								FROM $track_login_table
								WHERE login_user_id = '$_uid'
								ORDER BY login_date DESC LIMIT 1";
		$resLastLogin = api_sql_query($sqlLastLogin, __FILE__, __LINE__);
		if (!$resLastLogin)
			if (mysql_num_rows($resLastLogin) > 0)
			{
				$user_last_login_datetime = mysql_fetch_array($resLastLogin);
				$user_last_login_datetime = $user_last_login_datetime[0];
				api_session_register('user_last_login_datetime');
			}
		mysql_free_result($resLastLogin);
		event_login();
		if (api_is_platform_admin())
		{
			// decode all open event informations and fill the track_c_* tables
			include (api_get_path(LIBRARY_PATH).'stats.lib.inc.php');
			decodeOpenInfos();
		}
	}
} // end login -- if($submit)
else
{
	// only if login form was not sent because if the form is sent the user was
	// already on the page.
	event_open();
}
/*
-----------------------------------------------------------
	Header
	include the HTTP, HTML headers plus the top banner
-----------------------------------------------------------
*/

$help = "Clar";
$tool_name = get_lang("ViewOpenCourses");
Display :: display_header($tool_name, $help);

/*
==============================================================================
		FUNCTIONS

		display_anonymous_right_menu()
		display_anonymous_course_list()

		display_login_form()
		handle_login_failed()

		display_lost_password_info()
==============================================================================
*/

/*
-----------------------------------------------------------
	Display functions
-----------------------------------------------------------
*/

/**
 * Displays the right-hand menu for anonymous users:
 * login form, useful links, help section
 * Warning: function defines globals
 * @version 1.0.1
 */
function display_anonymous_right_menu()
{
	global $loginFailed, $plugins;

	$platformLanguage = api_get_setting('platformLanguage');

	$_uid = api_get_user_id();
	if ( !(isset($_uid) && $_uid) ) // only display if the user isn't logged in
	{
		api_display_language_form();
		display_login_form();

		if ($loginFailed)
			handle_login_failed();
		if (get_setting('allow_lostpassword') == 'true' OR api_get_setting('allow_registration') == 'true')
		{
			echo '<div class="menusection"><span class="menusectioncaption">'.get_lang('MenuUser').'</span><ul class="menulist">';
			if (get_setting('allow_registration') == 'true')
			{
				echo '<li><a href="'.api_get_path(WEB_CODE_PATH).'auth/inscription.php">'.get_lang('Reg').'</a></li>';
			}
			if (get_setting('allow_lostpassword') == 'true')
			{
				display_lost_password_info();
			}
			echo '</ul></div>';
		}
	}
	echo "<div class=\"menusection\">", "<span class=\"menusectioncaption\">".get_lang("MenuGeneral")."</span>";
	 echo "<ul class=\"menulist\">";

	$user_selected_language = $_SESSION["user_language_choice"];
	if (!isset ($user_selected_language))
		$user_selected_language = $platformLanguage;

	if(!file_exists('home/home_menu_'.$user_selected_language.'.html'))
	{
		include ('home/home_menu.html');
	}
	else
	{
		include('home/home_menu_'.$user_selected_language.'.html');
	}
	 echo '</ul>';
	echo '</div>';

	// Load appropriate plugins for this menu bar
	if (is_array($plugins['main_menu']))
	{
		foreach ($plugins['main_menu'] as $this_plugin)
		{
			include (api_get_path(SYS_PLUGIN_PATH)."$this_plugin/index.php");
		}
	}


	echo '<div class="note">';
	// includes for any files to be displayed below anonymous right menu
	if(!file_exists('home/home_notice_'.$user_selected_language.'.html'))
	{
		include ('home/home_notice.html');
	}
	else
	{
		include('home/home_notice_'.$user_selected_language.'.html');
	}
	echo '</div>';

}

/**
*	Reacts on a failed login:
*	displays an explanation with
*	a link to the registration form.
*
*	@version 1.0.1
*/
function handle_login_failed()
{
	$message = get_lang("InvalidId");
	if (api_is_self_registration_allowed())
		$message = get_lang("InvalidForSelfRegistration");
	echo "<div id=\"login_fail\">".$message."</div>";
}

/**
*	Adds a form to let users login
*	@version 1.1
*/
function display_login_form()
{
	$form = new FormValidator('formLogin');
	$renderer =& $form->defaultRenderer();
	$renderer->setElementTemplate('<div>{label}</div><div>{element}</div>');
	$renderer->setElementTemplate('<div>{element}</div>','submitAuth');
	$form->addElement('text','login',get_lang('UserName'),array('size'=>15));
	$form->addElement('password','password',get_lang('Pass'),array('size'=>15));
	$form->addElement('submit','submitAuth',get_lang('Ok'));
	$form->display();
}

/**
 * Displays a link to the lost password section
 */
function display_lost_password_info()
{
	echo '<li><a href="'.api_get_path(WEB_CODE_PATH).'auth/lostPassword.php">'.get_lang('LostPassword').'</a></li>';
}

/**
* Display general list of courses in a category.
* For anonymous (not logged in) visitors, this shows all courses
* "open for the world" (view right is true for anonymous guests).
* For logged-in platform users, this shows all courses
* open for the world and "open for the platform" (view right true for registered guests).
*
* @version 1.0
*/
function display_anonymous_course_list()
{
	$user_id = api_get_user_id();
	
	//init
	$web_course_path = api_get_path(WEB_COURSE_PATH);
	$category = $_GET['category'];
	$main_course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
	$main_category_table = Database :: get_main_table(MAIN_CATEGORY_TABLE);
	$platformLanguage = api_get_setting('platformLanguage');

	//get list of courses in category $category
	$sql_get_course_list = "SELECT * FROM $main_course_table cours
								WHERE category_code = '$category'
								ORDER BY UPPER(visual_code)";
	//removed: AND cours.visibility='".COURSE_VISIBILITY_OPEN_WORLD."'
	$sql_result_courses = api_sql_query($sql_get_course_list, __FILE__, __LINE__);

	while ($course_result = mysql_fetch_array($sql_result_courses))
	{
		$course_list[] = $course_result;
	}

	$sqlGetSubCatList = "
				SELECT t1.name,t1.code,t1.parent_id,t1.children_count,COUNT(DISTINCT t3.code) AS nbCourse
				FROM $main_category_table t1
				LEFT JOIN $main_category_table t2 ON t1.code=t2.parent_id
				LEFT JOIN $main_course_table t3 ON (t3.category_code=t1.code AND t3.visibility='".COURSE_VISIBILITY_OPEN_WORLD."')
				WHERE t1.parent_id ". (empty ($category) ? "IS NULL" : "='$category'")."
				GROUP BY t1.name,t1.code,t1.parent_id,t1.children_count ORDER BY t1.tree_pos";

	$resCats = api_sql_query($sqlGetSubCatList, __FILE__, __LINE__);
	$thereIsSubCat = FALSE;
	if (mysql_num_rows($resCats) > 0)
	{
		$htmlListCat = "<h4 style=\"margin-top: 0px;\">".get_lang("CatList")."</h4>"."<ul>";
		while ($catLine = mysql_fetch_array($resCats))
		{
			if ($catLine['code'] != $category)
			{
				$htmlListCat .= "<li>";

				$category_has_open_courses = category_has_open_courses($catLine['code']);
				if ($category_has_open_courses)
				{
					//the category contains courses accessible to anonymous visitors
					$htmlListCat .= "<a href=\"".$_SERVER['PHP_SELF']."?category=".$catLine['code']."\">".$catLine['name']."</a>";
					if (CONFVAL_showNumberOfChild)
					{
						$htmlListCat .= " (".$catLine[nbCourse]." ".get_lang("Courses").")";
					}
				}
				elseif ($catLine['children_count'] > 0)
				{
					//the category has children, subcategories
					$htmlListCat .= "<a href=\"".$_SERVER['PHP_SELF']."?category=".$catLine['code']."\">".$catLine['name']."</a>";
				}
				elseif (CONFVAL_showNodeEmpty)
				{
					$htmlListCat .= $catLine['name'];
				}
				$htmlListCat .= "</li>\n";
				$thereIsSubCat = true;
			}
			else
			{
				$htmlTitre = "<p>";
				if (CONFVAL_ShowLinkBackToTopOfTree)
				{
					$htmlTitre .= "<a href=\"".$_SERVER['PHP_SELF']."\">"."&lt;&lt; ".get_lang("BackToHomePage")."</a>";
				}
				if (!is_null($catLine['parent_id']) || (!CONFVAL_ShowLinkBackToTopOfTree && !is_null($catLine['code'])))
				{
					$htmlTitre .= "<a href=\"".$_SERVER['PHP_SELF']."?category=".$catLine['parent_id']."\">"."&lt;&lt; ".get_lang("Up")."</a>";
				}
				$htmlTitre .= "</p>\n";
				if ($category != "" && !is_null($catLine['code']))
				{
					$htmlTitre .= "<h3>".$catLine['name']."</h3>\n";
				}
				else
				{
					$htmlTitre .= "<h3>".get_lang("Categories")."</h3>\n";
				}
			}
		}
		$htmlListCat .= "</ul>\n";
	}
	echo $htmlTitre;
	if ($thereIsSubCat)
		echo $htmlListCat;
	while ($categoryName = mysql_fetch_array($resCats))
	{
		echo "<h3>", $categoryName['name'], "</h3>\n";
	}

	if (count($course_list) > 0)
	{
		if ($thereIsSubCat)
			echo "<hr size=\"1\" noshade=\"noshade\">\n";
		echo "<h4 style=\"margin-top: 0px;\">", get_lang("CourseList"), "</h4>\n", "<ul>\n";

		foreach ($course_list as $course)
		{
			$course_location_id = RolesRights::get_course_location_id($course['code']);
			if (isset($user_id) && $user_id)
			{
				$is_allowed_access = RolesRights::is_allowed(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id);
			}
			else
			{
				$is_allowed_access = RolesRights::is_allowed(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id);
			}
			
			if ($is_allowed_access)
			{
				echo "<li>\n", '<a href="'.$web_course_path.$course['directory'], '/">', $course['title'], '</a>', '<br/>', $course['visual_code'], ' &ndash; ', $course['tutor_name'], ((CONFVAL_showCourseLangIfNotSameThatPlatform && $course['course_language'] != $platformLanguage) ? ' &ndahs; '.$course['course_language'] : ''), "\n", "</li>\n";
			}
		}

		echo "</ul>\n";
	}
	else
	{
		// echo "<blockquote>",get_lang('_No_course_publicly_available'),"</blockquote>\n";
	}
	if ($category != "")
	{
		echo "<p>", "<a href=\"".$_SERVER['PHP_SELF']."\"><b>&lt;&lt;</b> ", get_lang("BackToHomePage"), "</a>", "</p>\n";
	}
}

/**
* Determines wether the category should be a hyperlink - indicating
* there are accessible courses underneath (open for world/platform).
*/
function category_has_open_courses($category)
{
	$user_id = api_get_user_id();
	$main_course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
	$sql_query = "SELECT * FROM $main_course_table WHERE category_code='$category'";
	$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
	while ($course = mysql_fetch_array($sql_result))
	{
		$course_location_id = RolesRights::get_course_location_id($course['code']);
		if (isset($user_id) && $user_id)
		{
			$is_allowed_access = RolesRights::is_allowed(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id);
		}
		else
		{
			$is_allowed_access = RolesRights::is_allowed(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id);
		}
		if ($is_allowed_access) return true; //at least one open course
	}

	return false;
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/
// Display courses and category list
echo '<div class="maincontent">';
echo '<div class="home_cats">';
if (DISPLAY_COURSES_TO_ANONYMOUS_USERS)
{
	display_anonymous_course_list();
}
echo '</div>';
echo '</div>';

// Right Menu
// language form, login section + useful weblinks
echo '<div class="menu">';
display_anonymous_right_menu();
echo '</div>';

/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
?>