<?php
/**
============================================================================== 
*	@package dokeos.forum
============================================================================== 
*/
// modified by Istvan Mandak, 2005.02
if (isset($_GET["lp"]))
{
	if(strcmp($_GET["lp"],"true")==0)
	{
		require_once "builder.php";		
	}
}
else
{
	if (isset($_REQUEST["lp"]))
	{
		if(strcmp($_REQUEST["lp"],"true")==0)
		{
			require_once "builder.php";		
		}
	}
}
// end of the included section

/***************************************************************************
                            veiwforum.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2001 The phpBB Group
    email                : support@phpbb.com

    $Id$

 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
include('extention.inc');
include('functions.php');
include('config.php');
require('auth.php');

$pagetitle = $l_viewforum;
$pagetype = "viewforum";
$forum = isset($_GET['forum']) ? $_GET['forum'] : -1;
if($forum == -1) header("Location: $url_phpbb");

$sql = "SELECT f.forum_type, f.forum_name 
        FROM `$tbl_forums` f
        WHERE forum_id = '$forum'";

$sql = "SELECT 	`f`.`forum_type`,
        `f`.`forum_name`,
        `g`.`id`	`idGroup`,
        `g`.`name` 	`nameGroup`
        FROM `".$tbl_forums."` `f`
        LEFT JOIN `".$tbl_student_group."` `g`
        ON `f`.`forum_id` = `g`.`forum_id`
        WHERE `f`.`forum_id` = '".$forum."'";

if(!$result = mysql_query($sql)) 
	error_die("An Error Occured<hr>Could not connect to the forums database.");
if(!$myrow = mysql_fetch_array($result,MYSQL_ASSOC))
	error_die("Error - The forum you selected does not exist. Please go back and try again.");

if ( is_null($myrow["idGroup"]) || ($myrow["idGroup"]==$_gid) )
{
	if(!is_null($myrow["idGroup"]))
	{
		require_once(api_get_library_path().'/groupmanager.lib.php');
		if( ! GroupManager::user_has_access($_uid,$_gid,GROUP_TOOL_FORUM) )
		{
			api_not_allowed();	
		}
	}
	// Am I member of the group ? or Group are open ?
	// if not Bye bye

	$forum_name = own_stripslashes($myrow["forum_name"]);

	// Note: page_header is included later on, because this page might need to send a cookie.

	if(($myrow["forum_type"] == 1) && !$user_logged_in && !$logging_in)
	{
		require('page_header.php');

	$forum_name = own_stripslashes($myrow["forum_name"]);

	?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
<tr bgcolor="<?php echo $color1?>" align="left">
<td align="center"><?php echo $l_private?></td>
</tr>

<tr>
<td><b>user name: &nbsp;</b></td>
<td><input type="text" name="username" size="25" maxlength="40" value="<?php echo $userdata[username]?>"></td>
</tr>

<tr>
<td><b>password: </b></td>
<td><input type="password" name="password" size="25" maxlength="25"></td>
</tr>

<tr bgcolor="<?php echo $color1?>" align="left">
<td align="center">
<input type="hidden" name="forum" value="<?php echo $forum?>">
<input type="submit" name="logging_in" value="<?php echo $l_enter?>">
</td>
</tr>

</table>
</form>

<?php
	require('page_tail.php');
	exit();
	}
	else
	{
		if ($logging_in)
		{
			if ($username == '' || $password == '')
			{
				error_die("$l_userpass $l_tryagain");
			}
			if (!check_username($username, $db))
			{
				error_die("$l_nouser $l_tryagain");
			}
			if (!check_user_pw($username, $password, $db))
			{
				error_die("$l_wrongpass $l_tryagain");
			}

			/* if we get here, user has entered a valid username and password combination. */
			$userdata = get_userdata($username, $db);
			$sessid = new_session($userdata["user_id"], $REMOTE_ADDR, $sesscookietime, $db);
			set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);

		}

		require('page_header.php');

		if ($myrow["forum_type"] == 1)
		{
			// To get here, we have a logged-in user. So, check whether that user is allowed to view
			// this private forum.

			if (!check_priv_forum_auth($userdata["user_id"], $forum, FALSE, $db))
			{
				error_die("$l_privateforum $l_noread");
			}

			// Ok, looks like we're good.
		}

	?>

<table border="0" cellpadding="1" cellspacing="1" width="100%">

<tr class="cell_header">
<td colspan="6"><b><?php echo $forum_name ?></b></td>
</tr>

<tr bgcolor="<?php echo $color1; ?>" align="left">
<td colspan="2">&nbsp;<?php echo $l_topic?></td>
<td width="9%" align="center"><?php echo $l_replies?></td>
<td width="20%" align="center">&nbsp;<?php echo $l_poster?></td>
<td width="8%" align="center"><?php echo $langSeen?></td>
<td width="15%" align="center"><?php echo $langLastMsg?></td>
</tr>

	<?php
	if(!$start) $start = 0;
	if (!settype($start, 'integer') || !settype($topics_per_page, 'integer')) die('Start or topics_per_page variables are not integers.');	//sanity check of integer vars

	$sql = "SELECT t.*, u.username, u2.username as last_poster, p.post_time
			FROM `$tbl_topics` t
			LEFT JOIN `$tbl_users` u ON t.topic_poster = u.user_id
			LEFT JOIN `$tbl_posts` p ON t.topic_last_post_id = p.post_id
			LEFT JOIN `$tbl_users` u2 ON p.poster_id = u2.user_id
			WHERE t.forum_id = '$forum'
			ORDER BY topic_time DESC LIMIT $start, $topics_per_page";

	$result = mysql_query($sql, $db)
		or error_die("</table></table>An Error Occured<hr>phpBB could not query the topics database.<br>$sql");

	$topics_start = $start;

	if($myrow = mysql_fetch_array($result,MYSQL_ASSOC))
	{
		do
		{
			echo"\n<tr>";

			$replys             = $myrow["topic_replies"];
			$last_post          = $myrow["post_time"];
			$last_post_datetime = $myrow["post_time"];

			//list($last_post_datetime, $null) = split("by", $last_post);
			list($last_post_date, $last_post_time) = split(" ", $last_post_datetime);
			list($year, $month, $day)              = explode("-", $last_post_date);
			list($hour, $min)                      = explode(":", $last_post_time);
			$last_post_time                        = mktime($hour, $min, 0, $month, $day, $year);

			if($replys >= $hot_threshold)
			{
				if($last_post_time < $last_visit) $image = "../img/forum.png";
				else                              $image = "../img/red_forum.png";
			}
			else
			{
				// Original phpBB statements
				//if($last_post_time < $last_visit) $image = $folder_image;
				//else                              $image = $newposts_image;

				// Claroline statements
				if($last_post_time < $last_visit)
				{
					$image = "../img/forum.png";
					$alt="";
				}
				else
				{
					$image = "../img/red_forum.png";
					$alt="";
				}
			}

			// MODIFIED BY UGENT, february 2004, Patrick Cool: thread locking
			if($myrow[topic_status] == 1)         $image = "../img/lockthread.gif";

			echo	"<td><img src=\"".$image."\" alt=\"".$alt."\"></td>\n";

			$topic_title = own_stripslashes($myrow["topic_title"]);
			$pagination = '';
			$start = '';
			$topiclink = "viewtopic.".$phpEx."?".api_get_cidreq()."&amp;gidReq=".$_gid."&topic=".$myrow["topic_id"]."&forum=".$forum;

			if($replys+1 > $posts_per_page)
			{
				$pagination .= "&nbsp;&nbsp;&nbsp;(<img src=\"../img/posticon.gif\">".$l_gotopage." ";
				$pagenr      = 1;
				$skippages   = 0;

				for($x = 0; $x < $replys + 1; $x += $posts_per_page)
				{
					$lastpage = (($x + $posts_per_page) >= $replys + 1);

					if($lastpage)
					{
						$start = "&start=$x&$replys";
					}
					else
					{
						if ($x != 0)
						{
							$start = "&start=$x";
						}

						$start .= "&" . ($x + $posts_per_page - 1);
					}

					if($pagenr > 3 && $skippages != 1)
					{
						$pagination .= ", ... ";
						$skippages = 1;
					}

					if ($skippages != 1 || $lastpage)
					{
						if ($x!=0) $pagination .= ", ";
						$pagination .= "<a href=\"".$topiclink.$start."\">".$pagenr."</a>";
					}

					$pagenr++;
				}
				$pagination .= ")";
			}

			$topiclink .= "&$replys";

			echo	"<td>\n",
					"&nbsp;",
					"<a href=\"",$topiclink,"\">",$topic_title,"</a>",$pagination,"\n",
					"</td>\n";

			echo	"<td align=\"center\">",$replys,"</td>\n",
					"<td align=\"center\">",$myrow["prenom"]," ",$myrow[nom],"</td>\n",
					"<td align=\"center\">",$myrow["topic_views"],"</td>\n",
					"<td align=\"center\">",$last_post,"</td>\n",
					"</tr>\n";
		}
		while($myrow = mysql_fetch_array($result,MYSQL_ASSOC));
	}
	else
	{
		echo "<td bgcolor=\"$color1\" colspan =\"6\" align=\"center\">",$l_notopics,"</td></tr>\n";
	}
	?>
	</table>
	<?php
	}

	/*--------------------------------------
					TOPICS PAGER
			(When there are to much topics 
				   for a single page)
	  --------------------------------------*/

	$sql = "SELECT count(*) AS total FROM `$tbl_topics` WHERE forum_id = '$forum'";

	$r = mysql_query($sql) or error_die("Error could not contact the database!");

	list($all_topics) = mysql_fetch_array($r);

	$count = 1;

	$next = $topics_start + $topics_per_page;
	$prev = $topics_start - $topics_per_page;
	$prev = $prev < 0 ? 0 : $prev;

	if($all_topics > $topics_per_page)
	{
		echo "<p align=\"right\">";
		if($topics_start > 0 )
		{
			echo	"<a href=\"viewforum.php?".api_get_cidreq()."&gidReq=".$_gid."&forum=",$forum,"&start=",$prev,"\">",
					$l_prevpage,
					"</a> |";
		}
			for($x = 0; $x < $all_topics; $x++)
			{
				if(!($x % $topics_per_page))
				{
					if($x == $topics_start)
					{
						echo "$count\n";
					}
					else
					{
						echo	"<a href=\"viewforum.php?".api_get_cidreq()."&gidReq=".$_gid."&forum=",$forum,"&start=",$x,"\">",
								$count,
								"</a>\n";
					}

					$count++;

				}
			}
		if($next < $all_topics)
		{
			echo	"| <a href=\"viewforum.php?".api_get_cidreq()."&gidReq=".$_gid."&forum=",$forum,"&start=",$next,"\">",
					$l_nextpage,
					"</a> ";
		}
		echo "</p>";		

	}
}
else
{
	echo "This is not available for you";
}
require('page_tail.php');
?>
