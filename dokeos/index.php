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
*	This is the index file displayed when a user arrives at Dokeos.
*
*	It dispalys:
*	- tree of courses and categories
*	- login form
*	- public menu
*
*	Search for
*	CONFIGURATION parameters
*	to modify settings
*
*	@todo rewrite code to separate display, logic, database code
*	@package dokeos.main
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

$langFile = array ('courses', 'index');
$cidReset = true; /* Flag forcing the 'current course' reset,
                   as we're not inside a course anymore  */
/*
-----------------------------------------------------------
	Included libraries
-----------------------------------------------------------
*/
//this includes main_api too:
include_once ("./main/inc/claro_init_global.inc.php");
//$this_section = SECTION_COURSES;

include_once (api_get_library_path()."/course.lib.php");
include_once (api_get_library_path()."/debug.lib.inc.php");
include_once (api_get_library_path()."/events.lib.inc.php");
include_once (api_get_library_path()."/system_announcements.lib.php");
include_once (api_get_library_path()."/text.lib.php");
include_once (api_get_library_path()."/groupmanager.lib.php");
include_once (api_get_library_path()."/formvalidator/FormValidator.class.php");
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
	$nameTools = api_get_setting('siteName');
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
	$uid = $_SESSION['_uid'];
	if (isset ($uid))
	{
		$sqlLastLogin = "SELECT UNIX_TIMESTAMP(login_date)
								FROM $track_login_table
								WHERE login_user_id = '$uid'
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
			include (api_get_library_path()."/stats.lib.inc.php");
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

Display :: display_header($nameTools, $help);

/*
==============================================================================
		FUNCTIONS

		display_anonymous_right_menu()
		display_applications_list()

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
				echo '<li><a href="index_user.php?go=register">'.get_lang('Reg').'</a></li>';
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
			include (api_get_path(PLUGIN_PATH)."$this_plugin/index.php");
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
	echo "<li><a href=\"main/auth/lostPassword.php\">".get_lang("LostPassword")."</a></li>";
}

/**
* Display list of courses in a category.
* (for anonymous users)
*
* Warning: this function defines globals.
* @version 1.0
*/
function display_applications_list()
{
	$_uid = api_get_user_id();
	$applications = load_applications();
	if (count($applications))
	{
		$html = array();
		$html[] = '<h4 style="margin-top: 0px;">'.get_lang('ApplicationList').'</h4>';
		$html[] = '<ul>';
		foreach ($applications as $application)
		{
			if (isset($_uid))
			{
				$html[]= '<li><a href="index_'. $application .'.php">'. get_lang(application_to_class($application)) .'</a></li>';
			}
			else
			{
				$html[]= '<li>'. get_lang(application_to_class($application)) .'</li>';
			}
		}
		
		if (isset($_uid))
		{
			$html[]= '<li><a href="index_repository_manager.php">'. get_lang('RepositoryManager') .'</a></li>';
		}
		else
		{
			$html[]= '<li>'. get_lang('RepositoryManager') .'</li>';
		}
		
		$html[] = '</ul>';
		
		echo implode($html, "\n");
	}
}

function category_has_open_courses($category)
{
	$main_course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
	$sql_query = "SELECT * FROM $main_course_table WHERE category_code='$category'";
	$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
	while ($course = mysql_fetch_array($sql_result))
	{
		$course_location_id = RolesRights::get_course_location_id($course['code']);
		$is_allowed_anonymous_access = RolesRights::is_allowed(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id);
		if ($is_allowed_anonymous_access) return true; //at least one open course
	}

	return false;
}

/**
 * Loads the applications installed on the system. Applications are classes
 * in the /application/lib subdirectory. Each application is a directory,
 * which in its turn contains a class file named after the application. For
 * instance, the weblcms application is the class Weblcms, defined in
 * /application/lib/weblcms/weblcms.class.php. Applications must extend the
 * Application class.
 */
function load_applications()
{
	$applications = array();
	$path = dirname(__FILE__).'/application/lib';
	if ($handle = opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$toolPath = $path.'/'. $file .'/'.$file.'_manager';
			if (is_dir($toolPath) && is_application_name($file))
			{
				require_once $toolPath.'/'.$file.'.class.php';
				if (!in_array($file, applications))
				{
					$applications[] = $file;
				}
			}
		}
		closedir($handle);
	}
	return $applications;
}

function is_application_name($name)
{
	return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
}

function application_to_class($application)
{
	return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/

echo '<div class="maincontent">';
if (!empty ($_GET['include']) && !strstr($_GET['include'], '/') && strstr($_GET['include'], '.html'))
{
	include ('./home/'.$_GET['include']);
	$pageIncluded = true;
}
else
{
	if(!file_exists('home/home_news_'.$user_selected_language.'.html'))
	{
	include ('home/home_top.html');
	}
	else
	{
	include('home/home_top_'.$user_selected_language.'.html');
	}
}

// Display System announcements
$announcement = $_GET['announcement'] ? $_GET['announcement'] : -1;
SystemAnnouncementManager :: display_announcements(VISIBLE_GUEST, $announcement);

// Display courses and category list
if (!$pageIncluded)
{
//	echo '<div class="clear">&nbsp;</div>';
	echo '<div class="home_cats">';
	if (DISPLAY_COURSES_TO_ANONYMOUS_USERS)
	{
		display_applications_list();
	}
	echo '</div>';

	echo '<div class="home_news">';
	if(!file_exists('home/home_news_'.$user_selected_language.'.html'))
	{
		include ('home/home_news.html');
	}
	else
	{
		include('home/home_news_'.$user_selected_language.'.html');
	}
	echo '</div>';

}
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