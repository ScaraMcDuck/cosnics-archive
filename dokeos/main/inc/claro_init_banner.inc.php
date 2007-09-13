<?php
/**
==============================================================================
*	This script contains the actual html code to display the "header"
*	or "banner" on top of every Dokeos page.
*
*	@package dokeos.include
==============================================================================
*/
echo '<div id="header">  <!-- header section start -->'."\n";
echo '<div id="header1"> <!-- top of banner with institution name/hompage link -->'."\n";

echo '<div id="institution">'."\n";
echo '<a href="'. api_get_path(WEB_PATH).'index.php" target="_top">'. api_get_setting('siteName'). '</a>';
echo '-';
echo '<a href="'. api_get_setting('InstitutionUrl').'" target="_top">'.api_get_setting('Institution').'</a>';
echo '</div>'."\n";

//not to let the header disappear if there's nothing on the left
echo '<div class="clear">&nbsp;</div>'."\n";

/*
-----------------------------------------------------------------------------
	Plugins for banner section
-----------------------------------------------------------------------------
*/
if (is_array($plugins['banner']))
{
	foreach ($plugins['banner'] as $this_plugin)
	{
		include (api_get_path(PLUGIN_PATH)."$this_plugin/index.php");
	}
}

$web_course_path = api_get_path(WEB_COURSE_PATH);
echo '</div> <!-- end of #header1 -->'."\n";
echo '<div id="header2">'."\n";
echo '<div id="Header2Right">'."\n";
echo '<ul>'."\n";

if ((api_get_setting('showonline','world') == "true" AND !$_uid) OR (api_get_setting('showonline','users') == "true" AND $_uid) OR (api_get_setting('showonline','course') == "true" AND $_uid AND $_cid))
{
	$statistics_database = Database :: get_statistic_database();
	$number = count(WhoIsOnline(api_get_user_id(), $statistics_database, 30));
	$online_in_course = who_is_online_in_this_course(api_get_user_id(), 30, $_course['id']);
	$number_online_in_course= count( $online_in_course );
	echo "<li>".get_lang('UsersOnline').": ";

	// Display the who's online of the platform
	if ((api_get_setting('showonline','world') == "true" AND !$_uid) OR (api_get_setting('showonline','users') == "true" AND $_uid))
	{
		echo "<a href='".api_get_path(WEB_PATH)."whoisonline.php' target='_top'>".$number."</a>";
	}

	// Display brackets if who's online of the campus AND who's online in the course are active
	if (api_get_setting('showonline','users') == "true" AND api_get_setting('showonline','course') == "true" AND $_course)
	{
		echo '(';
	}

	// Display the who's online for the course
	if ($_course AND api_get_setting('showonline','course') == "true")
	{
		echo "<a href='".api_get_path(REL_CLARO_PATH)."online/whoisonlinecourse.php' target='_top'>$number_online_in_course ".get_lang('InThisCourse')."</a>";
	}

	// Display brackets if who's online of the campus AND who's online in the course are active
	if (api_get_setting('showonline','users') == "true" AND api_get_setting('showonline','course') == "true" AND $_course)
	{
		echo ')';
	}

	echo '</li>';
}

echo '</ul>'."\n";
echo '</div>'."\n";
echo '<!-- link to campus home (not logged in)'."\n";
echo '<a href="'. api_get_path(WEB_PATH) .'index.php" target="_top">'. api_get_setting('siteName') .'</a>'."\n";
echo '-->'."\n";
//not to let the empty header disappear and ensure help pic is inside the header
echo '<div class="clear">&nbsp;</div>'."\n";

echo '</div><!-- End of header 2-->'."\n";


/*
-----------------------------------------------------------------------------
	User section
-----------------------------------------------------------------------------
*/
if ($_uid)
{

	echo '<div id="header3"> <!-- start user section line with name, my course, my profile, scorm info, etc -->'."\n";

	echo '<form method="get" action="'. api_get_path(WEB_PATH). 'index.php" class="banner_links" target="_top">'."\n";
	echo '<input type="hidden" name="logout" value="true"/>'."\n";
	echo '<input type="hidden" name="uid" value="'.$_uid.'"/>'."\n";
	echo '<div id="logout">'."\n";
	echo '<input type="submit" name="submit" value="'. get_lang("Logout"). '" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" class="logout"/>'."\n";
	echo '</div>'."\n";
	echo '</form>'."\n";

	global $user;

	$usermgr = new UserManager($_SESSION['_uid']);
	$user = $usermgr->get_user();

	$applications = load_applications();

	foreach($applications as $application)
	{
		if($GLOBALS['this_section'] == $application)
		{
			$link_class='class="here"';
		}
		else
		{
			$link_class='';
		}

		if ($application == 'personal_messenger')
		{
			$pmmgr = PersonalMessengerDataManager :: get_instance();
			$count = $pmmgr->count_unread_personal_message_publications($user);
		}
		else
		{
			$count = 0;
		}


		echo '<a '. $link_class .' href="'. api_get_path(WEB_PATH) .'index_'.$application.'.php" target="_top">';
		echo get_lang(application_to_class($application));
		echo ($count > 0 ? '&nbsp;('.$count.')' : null);
		echo '</a>&nbsp;'."\n";
	}

	if($GLOBALS['this_section'] == "myrepository")
	{
		$link_class='class="here"';
	}
	else
	{
		$link_class='';
	}

	echo '<a '. $link_class .' href="'. api_get_path(WEB_PATH) .'index_repository_manager.php" target="_top">';
	echo get_lang('MyRepository');
	echo '</a>&nbsp;'."\n";

	if($GLOBALS['this_section'] == "myaccount")
	{
		$link_class='class="here"';
	}
	else
	{
		$link_class='';
	}

	echo '<a '. $link_class .' href="'. api_get_path(WEB_PATH) .'index_user.php?go=account" target="_top">';
	echo get_lang('ModifyProfile');
	echo '</a>&nbsp;'."\n";

//	$pmmgr = new PersonalMessenger($user);
//	$count = $pmmgr->count_unread_personal_message_publications();
//
//	echo '<a '. $link_class .' href="'. api_get_path(WEB_PATH) .'index_personal_messenger.php" target="_top">';
//	echo get_lang('MyPMs');
//	echo ($count > 0 ? '&nbsp;('.$count.')' : null);
//	echo '</a>&nbsp;'."\n";

	if ($user->is_platform_admin())
	{
 		if($GLOBALS['this_section'] == "platform_admin")
		{
			$link_class='class="here"';
		}
		else
		{
			$link_class='';
		}
		echo '<a id="platform_admin" '.$link_class.' href="'.api_get_path(WEB_PATH).'index_admin.php" target="_top">';
		echo get_lang('PlatformAdmin');
		echo '</a>&nbsp;'."\n";
	}

	echo '</div> <!-- end of header3 (user) section -->'."\n";
}

if (isset ($nameTools) || is_array($interbredcrump))
{
	if (!isset ($_uid))
	{
		echo " ";
	}
	else
	{
		echo '&nbsp;&nbsp;<a href="'. api_get_path(WEB_PATH) .'index.php" target="_top">'. api_get_setting('siteName') .'</a>';
		if (isset ($_GET['coursePath']))
		{
			echo '&gt; <a href="'. api_get_path(WEB_PATH) .'"user_portal.php" target="_top">'. get_lang('MyCourses') .'</a>';
		}
	}
}

// else we set the site name bold
if (is_array($interbredcrump))
{
	foreach($interbredcrump as $breadcrumb_step)
	{
		echo '&nbsp;&gt; <a href="'. $breadcrumb_step['url'] .'" target="_top">'. $breadcrumb_step['name'] .'</a>'."\n";
	}
}

if (isset ($nameTools))
{
	if (!isset ($_uid))
	{
		echo '&nbsp;';
	}
	elseif (!defined('DOKEOS_HOMEPAGE') || !DOKEOS_HOMEPAGE)
	{
		if ($noPHP_SELF)
		{
			echo '&nbsp;&gt;&nbsp;'.$nameTools."\n";
		}
		else
		{
			echo ' &gt; <a href="'. htmlspecialchars($_SERVER['REQUEST_URI']) .'" target="_top">'. $nameTools .'</a>'."\n";
		}
	}
}

echo '<div class="clear">&nbsp;</div>'."\n";

if (isset ($dokeos_database_connection))
{
	// connect to the main database.
	// if single database, don't pefix table names with the main database name in SQL queries
	// (ex. SELECT * FROM `table`)
	// if multiple database, prefix table names with the course database name in SQL queries (or no prefix if the table is in
	// the main database)
	// (ex. SELECT * FROM `table_from_main_db`  -  SELECT * FROM `courseDB`.`table_from_course_db`)
	mysql_select_db($mainDbName, $dokeos_database_connection);
}

echo '</div> <!-- end of the whole #header section -->'."\n";
echo '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->'."\n";
echo '<!--   Begin Of script Output   -->'."\n";
