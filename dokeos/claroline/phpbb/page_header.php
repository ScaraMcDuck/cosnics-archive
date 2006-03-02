<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003-2005 Ghent University (UGent)
	Copyright (c) 2001-2002 Universite catholique de Louvain (UCL)
	Copyright (c) 2001 The phpBB Group
	
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
* This file is based on page_header.php of phpBB1.4,
* with many modifications.
*
* @package dokeos.forum
==============================================================================
*/
/**
============================================================================== 
*	@package dokeos.forum
============================================================================== 
*/
api_session_register("forum_id");

/* Who's Online Hack */
$IP = $REMOTE_ADDR;

if($pagetype == "index")
{
	$users_online = get_whosonline($IP, $userdata[username], 0, $db);
}
if($pagetype == "viewforum" || $pagetype == "viewtopic")
{
	$users_online = get_whosonline($IP, $userdata[username], $forum, $db);
}
if($pagetype == "admin")
{
	$header_image = "../$header_image";
}



$login_logout_link = make_login_logout_link($user_logged_in, $url_phpbb);

$langFile = "phpbb";


$is_allowedToEdit = $is_courseAdmin;


$nameTools = $l_forums;

$noPHP_SELF = true; //because  phpBB need always param IN URL

require_once(api_get_library_path().'/groupmanager.lib.php');

Display::display_header($nameTools,"For");
$tbl_group = Database::get_course_group_table();

/*
echo "<a href=\"./search.php?addterms=any&forum=all&sortby=p.post_time%20desc&searchboth=both&submit=Rechercher\">$langLastMsgs</a>";
*/

api_display_tool_title($l_forums);


/*================================================
  RELATE TO GROUP DOCUMENT AND SPACE FOR CLAROLINE
  ================================================*/


// Determine if uid is tutor for this course

$sqlTutor = mysql_query("SELECT tutor_id as tutor FROM course_rel_user
                         WHERE user_id='".$_uid."'
                         AND course_code='".$_cid."'") or die('Error in file '.__FILE__.' at line '.__LINE__);

while ($myTutor = mysql_fetch_array($sqlTutor))
{
	$tutorCheck = $myTutor['tutor'];
}


// Determine if forum category is Groups

$forumCatId = mysql_query("SELECT cat_id FROM `$tbl_forums`
                           WHERE forum_id='".$forum."'") or die('Error in file '.__FILE__.' at line '.__LINE__);

while ($myForumCat = mysql_fetch_array($forumCatId))
{
	$catId = $myForumCat['cat_id'];
}



// Show Group Documents and Group Space
// only if in Category 2 = Group Forums Category
if ( $catId==1 )
{
	$sql = "SELECT * FROM `$tbl_student_group` WHERE forum_id = $forum";
	$res = api_sql_query($sql,__FILE__,__LINE__);
	$obj = mysql_fetch_object($res);
	
	// group space links
	echo	"<br>\n",
			"<br>\n",
			"<a href=\"../group/group_space.php?gidReq=",$obj->id,"\">",
			$langGroupSpaceLink,
			"</a>\n";
	if ( GroupManager::user_has_access($_uid,$obj->id,GROUP_TOOL_DOCUMENTS))
	{	
		echo 	"&nbsp;&nbsp",
				"<a href=\"../document/document.php?gidReq=",$obj->id,"\">",
				$langGroupDocumentsLink,
				"</a>\n",
				"<br>\n",
				"<br>\n";
	}
}

/*========================================================================*/


if ($user_logged_in)
{
	// do PM notification.
	$last_visit_date = date("Y-m-d h:i", $last_visit);

	$username = addslashes($userdata[username]);

	$sql = "SELECT count(*) AS count
			FROM `$tbl_priv_msgs` p, `$tbl_users` u
			WHERE p.to_userid = u.user_id and p.msg_status = '0' and u.username = '$username'";

	if(!$result = mysql_query($sql, $db))
	{
		error_die("phpBB was unable to check private messages because " .mysql_error($db));
	}

	$row = @mysql_fetch_array($result);
	$new_message = $row[count];
	$word = ($new_message > 1) ? "messages" : "message";
	$privmsg_url = "$url_phpbb/viewpmsg.$phpEx";

	if ($new_message != 0)
	{
		eval($l_privnotify);
		print $privnotify;
	}
}

/*----------------------------------------
             BREADCRUMB TRAIL
 --------------------------------------*/


switch($pagetype)
{
	case 'index':

		$total_posts = get_total_posts("0", $db, "all");
		$total_users = get_total_posts("0", $db, "users");
		$sql = "SELECT username, user_id FROM `$tbl_users` WHERE user_level != -1 ORDER BY user_id DESC LIMIT 1";
		$res = mysql_query($sql, $db) or die('Error in file '.__FILE__.' at line '.__LINE__);
		$row = mysql_fetch_array($res);
		$newest_user = $row["username"];
		$newest_user_id = $row["user_id"];
		$profile_url = $url_phpbb."/bb_profile.".$phpEx."?mode=view&user=".$newest_user_id;
		$online_url = $url_phpbb."/whosonline.".$phpEx;

		eval($l_statsblock);
		// print $statsblock;
		// print_login_status($user_logged_in, $userdata[username], $url_phpbb);   // deactivated by CLAROLINE

		break;

	case 'viewforum':

		//echo "<h4>",$forum_name,"</h4>";

	case 'viewtopic':

		$total_forum = get_total_posts($forum, $db, 'forum');

		echo "\n";
		if ($catId == 1)
		{
			$sql = 'SELECT * FROM '.Database::get_course_table(GROUP_TABLE).' WHERE forum_id = '.$forum;
			$res = api_sql_query($sql,__FILE__,__LINE__);
			if( mysql_num_rows($res) == 1)
			{
				$obj = mysql_fetch_object($res);
				$group_url = '&amp;gidReq='.$obj->id;
			}
		}
		echo	"<a href=\"",$url_phpbb,"/index.",$phpEx,"\">",
				api_get_setting('siteName')," Forum Index",
				"</a>",
				$l_separator,
				"<a href=\"",$url_phpbb,"/viewforum.",$phpEx,"?forum=",$forum,"&",$total_forum,$group_url,"\">",
				stripslashes($forum_name),
				"</a>";

		if($pagetype != "viewforum")
		{
			echo $l_separator;
		}

		echo $topic_subject;

	echo "\n";

		break;
}

/*----------------------------------------
                 TOOL BAR
 --------------------------------------*/


// go to administration panel

if( $is_allowed[EDIT_RIGHT] )
{
	$toolBar [] =	"<a href=\"../forum_admin/forum_admin.php\">".$langAdm."</a>";
}


switch($pagetype)
{
	// 'index' is covered by default

	case 'newtopic':

		$toolBar [] =	"$lang_post_new_topic_in : ".
						"<a href=\"".$url_phpbb."/viewforum.".$phpEx."?forum=".$forum."\">".
						$forum_name.
						"</a>\n";
		break;

	case 'viewforum':

		$toolBar [] =	"<a href=\"newtopic.php?forum=".$forum."\">".$langNewTopic."</a>";

		break;

	case 'viewtopic':

		// ADDED BY UGENT, Patrick Cool, march 2004, flat/threaded view
		if (EMPTY($forumview) or $forumview=="flat")
			$toolBar []= "<a href='".$_SERVER['PHP_SELF']."?topic=".$topic."&forum=".$forum."&forumview=threaded'>".$lang_threaded_view."</a>";
		if ($forumview=="threaded")
			$toolBar []= "<a href='".$_SERVER['PHP_SELF']."?topic=".$topic."&forum=".$forum."&forumview=flat'>".$lang_flat_view."</a>";
		// END ADDED BY UGENT, Patrick Cool, march 2004, flat/threaded view
		
		if($lock_state != 1)
		{
			$toolBar [] =	"<a href=\"$url_phpbb/reply.php?topic=".$topic."&forum=".$forum."\">".
							$langAnswer.
							"</a>\n";
			// ADDED BY UGENT, Patrick Cool, february 2004, thread locking
			if ($status[$dbname] ==1 or $status[$dbname]==2) // if prof or allowed to admin
				{
				$toolBar [] =	"<a href='".$_SERVER['PHP_SELF']."?topic=".$topic."&forum=".$forum."&modify=lock'>$lang_lock</a>";
				}
			// END ADDED BY UGENT, Patrick Cool, february 2004, thread locking
		}
		else
		{
			// MODIFIED BY UGENT, Patrick Cool, february 2004, thread locking
			// original line: $toolBar [] =	"<img src=\"".$reply_locked_image."\" border=\"0\">\n";
			$toolBar [] =	$langAnswer;
			// END MODIFIED BY UGENT, Patrick Cool, february 2004, thread locking
			// ADDED BY UGENT, Patrick Cool, february 2004, thread locking
			if ($status[$dbname] ==1 or $status[$dbname]==2) // if prof or allowed to admin
				{
				$toolBar [] =	"<a href='".$_SERVER['PHP_SELF']."?topic=".$topic."&forum=".$forum."&modify=unlock'>$lang_unlock</a>";
				}
			// END ADDED BY UGENT, Patrick Cool, february 2004, thread locking

		}

		$toolBar [] =	"<a href=\"newtopic.php?forum=".$forum."\">".$langNewTopic."</a>";

		break;

	// 'Register' is covered by default

	default:
		break;
}

if (is_array($toolBar)) $toolBar = implode(" | ", $toolBar);


echo "<p align=\"right\">",$toolBar,"</p>";


?>