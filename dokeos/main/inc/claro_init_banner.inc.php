<?php
/**
==============================================================================
*	This script contains the actual html code to display the "header"
*	or "banner" on top of every Dokeos page.
*
*	@package dokeos.include
==============================================================================
*/
?>
<div id="header">  <!-- header section start -->
<div id="header1"> <!-- top of banner with institution name/hompage link -->

<div id="institution">
<a href="<?php echo api_get_path(WEB_PATH);?>index.php" target="_top"><?php echo api_get_setting('siteName') ?></a>
-
<a href="<?php echo api_get_setting('InstitutionUrl') ?>" target="_top"><?php echo api_get_setting('Institution') ?></a>
</div>

<?php
/*
-----------------------------------------------------------------------------
	Course title section
-----------------------------------------------------------------------------
*/
if (isset ($_cid))
{
	//Put the name of the course in the header
	?>
	<div id="my_courses"><a href="<?php echo api_get_path(WEB_COURSE_PATH).$_course['path']; ?>/index.php" target="_top">
	<?php

	echo $_course['name']." ";
	if (api_get_setting("display_coursecode_in_courselist") == "true")
	{
		echo $_course['official_code'];
	}
	if (api_get_setting("display_coursecode_in_courselist") == "true" AND api_get_setting("display_teacher_in_courselist") == "true")
	{
		echo " - ";
	}
	if (api_get_setting("display_teacher_in_courselist") == "true")
	{
		echo $_course['titular'];
	}
	echo "</a></div>";
}
elseif (isset ($nameTools) && $langFile != 'course_home')
{
	//Put the name of the user-tools in the header
	if (!isset ($_uid))
		echo " ";
	elseif(!$noPHP_SELF)
	{
		echo "<div id=\"my_courses\"><a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq(), "\" target=\"_top\">", $nameTools, "</a></div>", "\n";
	}
	else
	{
		echo "<div id=\"my_courses\">$nameTools</div>\n";
	}
}
//not to let the header disappear if there's nothing on the left
 echo '<div class="clear">&nbsp;</div>';

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

/*
-----------------------------------------------------------------------------
	External link section
-----------------------------------------------------------------------------
*/
if ($_course['extLink']['name'] != "") /* ---  --- */
{
	echo " / ";
	if ($_course['extLink']['url'] != "")
	{
		echo "<a href=\"".$_course['extLink']['url']."\" target=\"_top\">";
		echo $_course['extLink']['name'];
		echo "</a>";
	}
	else
		echo $_course['extLink']['name'];
}
echo "</div> <!-- end of #header1 -->";


echo '<div id="header2">';
echo '<div id="Header2Right">';
echo '<ul>';

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
if ($_uid)
{
	if (api_is_course_admin() && is_student_view_enabled())
	{
		echo '<li>|';
		api_display_tool_view_option($_GET['isStudentView']);
		echo '</li>';
	}
}
if ( api_is_allowed_to_edit() )
{
	if( $help != null)
	{
	// Show help
	?>
	<li>|
	<a href="#" onclick="MyWindow=window.open('<?php echo api_get_path(WEB_CODE_PATH)."help/help.php"; ?>?open=<?php echo $help; ?>','MyWindow','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600,left=200,top=20'); return false;">
	<img src="<?php echo api_get_path(WEB_CODE_PATH); ?>img/buoy.gif" style="vertical-align: middle;" alt="<?php echo get_lang("Help") ?>"/>&nbsp;<?php echo get_lang("Help") ?></a>
	</li>
	<?php
	}
}
?>
		</ul>
	</div>
<!-- link to campus home (not logged in)
	<a href="<?php echo api_get_path(WEB_PATH); ?>index.php" target="_top"><?php echo api_get_setting('siteName'); ?></a>
 -->
<?php
//not to let the empty header disappear and ensure help pic is inside the header
echo "<div class=\"clear\">&nbsp;</div>";
?>
</div> <!-- End of header 2-->

<?php
/*
-----------------------------------------------------------------------------
	User section
-----------------------------------------------------------------------------
*/
if ($_uid)
{
	?>
	<div id="header3"> <!-- start user section line with name, my course, my profile, scorm info, etc -->

	<form method="get" action="<?php echo api_get_path(WEB_PATH); ?>index.php" class="banner_links" target="_top">
	<input type="hidden" name="logout" value="true"/>
	<input type="hidden" name="uid" value="<?php echo $_uid; ?>"/>
	<div id="logout">
	<input type="submit" name="submit" value="<?php echo get_lang("Logout"); ?>"
	onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'"
	class="logout"/>
	</div>
	</form>

<?php
if($GLOBALS["this_section"] == "mycourses")
	{
	$link_class='class="here"';
	}
	else
	{
	$link_class='';
	}
?>
	<a <?php echo $link_class ?> href="<?php echo api_get_path(WEB_PATH); ?>index_weblcms.php" target="_top">
	<?php echo get_lang("MyCourses"); ?></a>

<?php
if($GLOBALS["this_section"] == "myrepository")
	{
	$link_class='class="here"';
	}
	else
	{
	$link_class='';
	}
?>
	<a <?php echo $link_class ?> href="<?php echo api_get_path(WEB_PATH); ?>index_repository_manager.php" target="_top">
	<?php echo get_lang("MyRepository"); ?></a>

<?php
if($GLOBALS["this_section"] == "myprofile")
	{
	$link_class='class="here"';
	}
	else
	{
	$link_class='';
	}
?>
	<a <?php echo $link_class ?> href="<?php echo $clarolineRepositoryWeb ?>auth/profile.php<?php if(!empty($_course['path'])) echo '?coursePath='.$_course['path'].'&amp;courseCode='.$_course['official_code']; ?>" target="_top">
	<?php echo get_lang("ModifyProfile"); ?></a>

<?php
if($GLOBALS["this_section"] == "myagenda")
	{
	$link_class='class="here"';
	}
	else
	{
	$link_class='';
	}
?>
	<a <?php echo $link_class ?> href="<?php echo $clarolineRepositoryWeb ?>calendar/myagenda.php<?php if(!empty($_course['path'])) echo '?coursePath='.$_course['path'].'&amp;courseCode='.$_course['official_code']; ?>" target="_top">
	<?php echo get_lang("MyAgenda"); ?></a>

<?php
	if (api_is_platform_admin())
	{
 	if($GLOBALS["this_section"] == "platform_admin")
		{
		$link_class='class="here"';
		}
		else
		{
		$link_class='';
		}
		echo "<a id=\"platform_admin\" ".$link_class." href=\"".$rootAdminWeb."\" target=\"_top\">".get_lang("PlatformAdmin")."</a>";
	}

?>
	</div> <!-- end of header3 (user) section -->

<?php
}
?>

	<div id="header4">
	<div id="toolshortcuts"><?php
  if(api_get_setting('show_toolshortcuts')=="true") {
  	require_once('tool_navigation_menu.inc.php');
  	show_navigation_tool_shortcuts();
  	}  ?></div>
<?php

if (isset ($_cid))
{
	?>
	<a href="<?php echo api_get_path(WEB_PATH) ?>index.php" target="_top">
	<?php echo api_get_setting('siteName'); ?></a> &gt;
	<?php
		if (isset ($_uid))
		{
			echo "<a href=\"".api_get_path(WEB_PATH)."user_portal.php\" target=\"_top\">";
			echo get_lang('MyCourses');
			echo "</a> &gt;";
		}
	?>
	<a href="<?php echo $web_course_path . $_course['path']; ?>/index.php" target="_top">
	<?php
	echo get_lang('CourseHomepageLink');
	echo '</a>';
}

// if name tools or interbredcrump defined, we don't set the site name bold

elseif (isset ($nameTools) || is_array($interbredcrump))
{
	if (!isset ($_uid)):
		echo " ";
	else: ?>
		<a href="<?php echo api_get_path(WEB_PATH); ?>index.php" target="_top"><?php echo api_get_setting('siteName');?></a>
		<?php if (isset ($_GET['coursePath'])): ?>
			&gt; <a href="<?php echo api_get_path(WEB_PATH); ?>user_portal.php" target="_top"><?php echo get_lang('MyCourses');?></a>
		<?php endif; ?>
	<?php endif;
}

// else we set the site name bold

if (is_array($interbredcrump))
{
	foreach($interbredcrump as $breadcrumb_step)
	{
		echo " &gt; <a href=\"", $breadcrumb_step['url'], "\" target=\"_top\">", $breadcrumb_step['name'], "</a>\n";
	}
}

if (isset ($nameTools) && $langFile != 'course_home')
{
	if (!isset ($_uid))
		echo " ";
	elseif (!defined('DOKEOS_HOMEPAGE') || !DOKEOS_HOMEPAGE)
	{
		if ($noPHP_SELF)
			echo " &gt; $nameTools\n";
		else
			echo ' &gt; <a href="' . htmlspecialchars($_SERVER['REQUEST_URI']) . "\" target=\"_top\">$nameTools</a>\n";
	}
}
?>
		<div class="clear">&nbsp;</div>
	</div><!-- end of header4 -->

<?php
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
?>

</div> <!-- end of the whole #header section -->

<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->

<?php
/*
-----------------------------------------------------------------------------
	"call for chat" module section
-----------------------------------------------------------------------------
*/
$chat = strpos($_SERVER['PHP_SELF'], 'chat_banner.php');
if (!$chat)
{
	include_once (api_get_library_path()."/online.inc.php");
	echo $accept;
	$chatcall = chatcall();
	if ($chatcall)
	{
		Display :: display_normal_message($chatcall);
	}
}

/*
-----------------------------------------------------------------------------
	Navigation menu section
-----------------------------------------------------------------------------
*/

if(api_get_setting("show_navigation_menu") == "true")
{

 api_show_course_navigation_menu($_GET['isHidden']);
 if (isset($_cid) )
	{
	echo '<div id="menuButton">';
 	echo $output_string_menu;
 	echo '</div>';

		if(isset($_SESSION['hideMenu']))
		{
		if($_SESSION['hideMenu'] =="shown")
		{
 			if (isset($_cid) )
                	{
			echo '<div id="centerwrap"> <!-- start of #centerwrap -->';
			echo '<div id="center"> <!-- start of #center -->';
			}
		}
 	}
 	else
 	{
		if (isset($_cid) )
		{
		echo '<div id="centerwrap"> <!-- start of #centerwrap -->';
		echo '<div id="center"> <!-- start of #center -->';
		}
 	}
 }
}

?>
<!--   Begin Of script Output   -->