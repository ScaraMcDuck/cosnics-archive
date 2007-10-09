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
/*
==============================================================================
	   INIT SECTION
==============================================================================
*/

define('DOKEOS_HOMEPAGE', true);
include_once ("./main/inc/claro_init_global.inc.php");
include_once (api_get_library_path()."/course.lib.php");
include_once (api_get_library_path()."/debug.lib.inc.php");
include_once (api_get_library_path()."/events.lib.inc.php");
include_once (api_get_library_path()."/system_announcements.lib.php");
include_once (api_get_library_path()."/text.lib.php");
include_once (api_get_library_path()."/groupmanager.lib.php");
include_once (api_get_library_path()."/formvalidator/FormValidator.class.php");
api_use_lang_files('courses', 'index');
$nameTools = api_get_setting('siteName');

/*
-----------------------------------------------------------
	Header
	include the HTTP, HTML headers plus the top banner
-----------------------------------------------------------
*/

$help = "Clar";

Display :: display_header($nameTools, $help);

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
				$html[]= '<li><a href="run.php?application='. $application .'">'. get_lang('App'.application_to_class($application)) .'</a></li>';
			}
			else
			{
				$html[]= '<li>'. get_lang('App'.application_to_class($application)) .'</li>';
			}
		}

		if (isset($_uid))
		{
			$html[]= '<li><a href="index_repository_manager.php">'. get_lang('AppRepositoryManager') .'</a></li>';
		}
		else
		{
			$html[]= '<li>'. get_lang('AppRepositoryManager') .'</li>';
		}

		$html[] = '</ul>';

		echo implode($html, "\n");
	}
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
	display_applications_list();
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