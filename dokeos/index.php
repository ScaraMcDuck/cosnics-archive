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
include_once ("./main/inc/global.inc.php");
include_once Path :: get_path(WEB_LIB_PATH)."html/formvalidator/FormValidator.class.php";
Translation :: set_application('general');
$nameTools = $adm->retrieve_setting_from_variable_name('site_name', 'admin')->get_value();

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
	global $loginFailed, $plugins, $adm;

	$platformLanguage = $adm->retrieve_setting_from_variable_name('platform_language', 'admin')->get_value();

	$_uid = api_get_user_id();
	if ( !(isset($_uid) && $_uid) ) // only display if the user isn't logged in
	{
		// TODO: New Language form
		//api_display_language_form();
		display_login_form();

		if ($loginFailed)
			handle_login_failed();
		if ($adm->retrieve_setting_from_variable_name('allow_password_retrieval')->get_value() == 'true' OR $adm->retrieve_setting_from_variable_name('allow_registration')->get_value() == 'true')
		{
			echo '<div class="menusection"><span class="menusectioncaption">'.Translation :: get_lang('MenuUser').'</span><ul class="menulist">';
			if ($adm->retrieve_setting_from_variable_name('allow_registration')->get_value() == 'true')
			{
				echo '<li><a href="index_user.php?go=register">'.Translation :: get_lang('Reg').'</a></li>';
			}
			if ($adm->retrieve_setting_from_variable_name('allow_password_retrieval')->get_value() == 'true')
			{
				echo '<li><a href="index_user.php?go=reset_password">'.Translation :: get_lang('LostPassword').'</a></li>';
			}
			echo '</ul></div>';
		}
	}
	echo "<div class=\"menusection\">", "<span class=\"menusectioncaption\">".Translation :: get_lang("MenuGeneral")."</span>";
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
	$message = Translation :: get_lang("InvalidId");
	// TODO: Replace this by setting from DB.
	//if (api_is_self_registration_allowed())
	//	$message = Translation :: get_lang("InvalidForSelfRegistration");
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
	$form->addElement('text','login',Translation :: get_lang('UserName'),array('size'=>15));
	$form->addElement('password','password',Translation :: get_lang('Pass'),array('size'=>15));
	$form->addElement('submit','submitAuth',Translation :: get_lang('Ok'));
	$form->display();
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
	$applications = Application::load_all();
	if (count($applications))
	{
		$html = array();
		$html[] = '<h4 style="margin-top: 0px;">'.Translation :: get_lang('ApplicationList').'</h4>';
		$html[] = '<ul>';
		foreach ($applications as $application)
		{
			if (isset($_uid))
			{
				$html[]= '<li><a href="run.php?application='. $application .'">'. Translation :: get_lang('App'.Application::application_to_class($application)) .'</a></li>';
			}
			else
			{
				$html[]= '<li>'. Translation :: get_lang('App'.Application::application_to_class($application)) .'</li>';
			}
		}

		if (isset($_uid))
		{
			$html[]= '<li><a href="index_repository_manager.php">'. Translation :: get_lang('AppRepositoryManager') .'</a></li>';
		}
		else
		{
			$html[]= '<li>'. Translation :: get_lang('AppRepositoryManager') .'</li>';
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

// TODO: Reimplement system announcements
// Display System announcements
//$announcement = $_GET['announcement'] ? $_GET['announcement'] : -1;
//SystemAnnouncementManager :: display_announcements(VISIBLE_GUEST, $announcement);

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