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
$forum = intval($_GET['forum']);
$topic = intval($_GET['topic']);
$postid = isset($_GET['postid']) ? intval($_GET['postid']) : null;
$forumview = isset($_GET['forumview']) ? $_GET['forumview'] : null;
// end of the included section

/***************************************************************************
                            viewtopic.php  -  description
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
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);

// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
include('../resourcelinker/resourcelinker.inc.php');
// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker

// ADDED BY Denes Nagy, Sept 2004, to deal with the situation if someone comes from Learning Path builder
session_unregister('addedresource');
unset($addedresource);
// END ADDED BY Denes Nagy, Sept 2004

$pagetitle = $l_topictitle;
$pagetype = "viewtopic";

$sql = "SELECT f.forum_type, f.forum_name FROM `$tbl_forums` f, `$tbl_topics` t 
        WHERE (f.forum_id = '$forum') AND (t.topic_id = '$topic')
        AND (t.forum_id = f.forum_id)";

$result = mysql_query("SELECT f.forum_type, f.forum_name
                       FROM `$tbl_forums` f, `$tbl_topics` t 
                       WHERE (f.forum_id = '$forum') 
                       AND (t.topic_id = '$topic')
                       AND (t.forum_id = f.forum_id)", $db) 
          or error_die("An Error Occured<hr>Could not connect to the forums database.");

$myrow = mysql_fetch_array($result)
         or error_die("Error - The forum/topic you selected does not exist. Please go back and try again.");

$forum_name = own_stripslashes($myrow[forum_name]);

// Note: page_header is included later on, because this page might need to send a cookie.

if(($myrow[forum_type] == 1) && !$user_logged_in && !$logging_in) 
{
?>
<FORM ACTION="<?php echo $_SERVER['PHP_SELF'];?>" METHOD="POST">

<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $tablewidth?>">

<TR>

<TD BGCOLOR="<?php echo $table_bgcolor?>">

<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="1" WIDTH="100%">

<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
<TD ALIGN="CENTER"><?php echo $l_private?></TD>
</TR>

<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
<TD ALIGN="CENTER">
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0">
<TR>
<TD><b>User Name: &nbsp;</b></TD>
<TD><INPUT TYPE="TEXT" NAME="username" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[username]?>">
</TD>
</TR><TR>
<TD><b>Password: </b></TD>
<TD><INPUT TYPE="PASSWORD" NAME="password" SIZE="25" MAXLENGTH="25"></TD>
</TR>

</TABLE>

</TD>

</TR>

<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
<TD ALIGN="CENTER">
<INPUT TYPE="HIDDEN" NAME="forum" VALUE="<?php echo $forum?>">
<INPUT TYPE="HIDDEN" NAME="topic" VALUE="<?php echo $topic?>">
<INPUT TYPE="SUBMIT" NAME="logging_in" VALUE="<?php echo $l_enter?>">
</TD>
</TR>

</TABLE>

</TD>
</TR>

</TABLE>

</FORM>
<?php
require('page_tail.'.$phpEx);
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
		
		$sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);	
		
		set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
	}



	if ($myrow[forum_type] == 1)
	{
		// To get here, we have a logged-in user. So, check whether that user is allowed to view
		// this private forum.
		
		if (!check_priv_forum_auth($userdata[user_id], $forum, FALSE, $db))
		{
			include('page_header.'.$phpEx);
			error_die("$l_privateforum $l_noread");
		}
	
		// Ok, looks like we're good.
	}

	$sql = "SELECT topic_title, topic_status FROM `$tbl_topics` WHERE topic_id = '$topic'";

	$total = get_total_posts($topic, $db, "topic");

	if($total > $posts_per_page)
	{
		$times = 0;
		for($x = 0; $x < $total; $x += $posts_per_page)
		$times++;
		$pages = $times;
	}

	// ADDED BY UGENT, Patrick Cool, february 2004
	//  This is the main lock/unlock mechanism
	// I had to put it here because it should be treated before $lock_state=$myrow[topic_status];
	if ($modify=="unlock")
		{
		$lockquery="UPDATE `$tbl_topics` SET topic_status=0 WHERE topic_id=$topic";
		mysql_query($lockquery) or die ("Error: ".mysql_error());
		$modify=""; 
		}
	if ($modify=="lock")
		{
		$lockquery="UPDATE `$tbl_topics` SET topic_status=1 WHERE topic_id=$topic";
		mysql_query($lockquery) or die ("Error: ".mysql_error());
		$modify=""; 
		}
	// END ADDED BY UGENT, Patrick Cool, february 2004


	$result = mysql_query($sql, $db) OR error_die("<big>An Error Occured<big><hr>Could not connect to the forums database.");

	$myrow = mysql_fetch_array($result);
	$topic_subject = own_stripslashes($myrow[topic_title]);
	$lock_state = $myrow[topic_status];

	include('page_header.'.$phpEx);

	// MODIFIED BY UGENT, Patrick Cool, march 2004
	// and $forumview!=="threaded" was added to the if condition because we do not want the pagination to appear when in threaded view. 
	if($total > $posts_per_page and $forumview!=="threaded")
	{
		echo "<table border=0 align=center>";
		$times = 1;
		echo "<tr align=\"left\"><td>",$l_gotopage," ( ";
		$last_page = $start - $posts_per_page;

		if($start > 0)
		{
			echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&topic=$topic&forum=$forum&start=$last_page\">",$l_prevpage,"</a> ";
		}

		for($x = 0; $x < $total; $x += $posts_per_page)
		{
			if($times != 1)
			echo " | ";

			if($start && ($start == $x))
			{
				echo $times;
			}

			elseif($start == 0 && $x == 0)
			{
				echo "1";
			}
			else
			{
				echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&mode=viewtopic&topic=$topic&forum=$forum&start=$x\">",$times,"</a>\n";
			}

			$times++;
		}

		if(($start + $posts_per_page) < $total)
		{
			$next_page = $start + $posts_per_page;
			echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&topic=$topic&forum=$forum&start=$next_page\">",$l_nextpage,"</a>\n";
		}

		echo	" )\n",
				"</td>\n",
				"</tr>\n",
				"</table>\n";
	}
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%">
<tr class="cell_header">
<td colspan="2"><b><?php echo  $topic_subject ?></b> 
  <?php 
  // ADDED BY UGENT, Patrick Cool, february 2004, thread locking
  if ($lock_state==1) echo "($lang_locked)";
  // END ADDED BY UGENT, Patrick Cool, february 2004, thread locking
  ?>
</td>

</tr>

<?php

	if(isset($start))
	{
		if (!settype($start, 'integer') || !settype($posts_per_page, 'integer')) die('Start or posts_per_page variables are not integers.');	//sanity check of integer vars
		// MODIFIED BY UGENT, Patrick Cool, march 2004, flat/threaded view
		// added pt.post_title tot the sql statement
		$sql = "SELECT p.*, pt.post_text, pt.post_title FROM `$tbl_posts` p, `$tbl_posts_text` pt 
		        WHERE topic_id = '$topic' 
		        AND p.post_id = pt.post_id
		        ORDER BY post_id LIMIT $start, $posts_per_page";
	}
	else
	{
		if (!settype($posts_per_page, 'integer')) die('Posts_per_page variable is not integer.');	//sanity check of integer vars
		// MODIFIED BY UGENT, Patrick Cool, march 2004, flat/threaded view
		// added pt.post_title tot the sql statement
		$sql = "SELECT p.*, pt.post_text, pt.post_title 
		        FROM `$tbl_posts` p, `$tbl_posts_text` pt
		        WHERE topic_id = '$topic'
		        AND p.post_id = pt.post_id
		        ORDER BY post_id LIMIT $posts_per_page";
	}
	
	// ADDED BY UGENT, Patrick Cool, march 2004, flat/threaded view
	// ********** START OF THREADED VIEW CODE ***************
	// only use this when we want a threaded view, so when the querystring $view = threaded
	if ($forumview=="threaded")
	{
		echo "<tr align='left'><td colspan='2' bgcolor='$color1'>";
		// if there is no $postid in the querystring, we should display the root message, thus the first message
		// when you sort ascending. $postid is empty in the querystring when you come from viewforum.php, or when
		// you lock or unlock the message (as course manager)
		if (!isset($postid))
		{
			$sqlselectroot = "SELECT post_id FROM `$tbl_posts` WHERE topic_id=".$topic." ORDER BY post_id ASC" ;
			$resultselectroot=mysql_query($sqlselectroot, $db) OR error_die("<big>An Error Occured</big><hr>Could not connect to the Posts database. $sqlselectroot");
			$myrowselectroot = mysql_fetch_array($resultselectroot);
			$postid=$myrowselectroot['post_id'];
		}
		$sqldisplaymessage = "SELECT p.*, pt.* FROM `$tbl_posts` p, `$tbl_posts_text` pt WHERE p.post_id = pt.post_id AND p.post_id = '".$postid."'";
		$resultdisplaymessage = mysql_query($sqldisplaymessage, $db) OR error_die("<big>An Error Occured</big><hr>Could not connect to the Posts database. $sqldisplaymessage");
		$myrowdisplaymessage = mysql_fetch_array($resultdisplaymessage);
		//we display the title of the post (post_title in table posts) and if this is empty (this can be the case
		//when adding the threaded / flat view to a course which already has fortum topics. It is empty since this
		//database field is added afterwards, we display re: topic_title
		$result = mysql_query($sql, $db) OR error_die("<big>An Error Occured</big><hr>Could not connect to the Posts database. $sql");
		$myrow = mysql_fetch_array($result);
		if (!IS_NULL($myrowdisplaymessage["post_title"]))
		{
			echo "<b>".$myrowdisplaymessage["post_title"]."</b><br>";
		}
		else
		{
			echo "<b>re: ".$topic_subject."</b><br>";
		}
		$myrowdisplaymessage["post_text"] = api_parse_tex($myrowdisplaymessage["post_text"]);
		echo $myrowdisplaymessage["post_text"];
		echo "</td></tr>";
		
		// ADDED BY UGENT, Patrick Cool, march 2004, resource linker	
		echo "<tr><td colspan='2'>"; 
		display_added_resources("Forum", $myrowdisplaymessage["post_id"]);
		echo "</td></tr>";
		// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker 	
		
		// here we start a new row which contains the author info of the post and the reply to this post notice.
		echo 	"<tr align='left'><td bgcolor='$color2'>".$lang_author.": ",
				$myrowdisplaymessage["prenom"]." ".$myrowdisplaymessage["nom"],
				"</td><td align='right' bgcolor='$color2'>";
		if($lock_state != 1)
			{ echo "<a href='reply.php?".api_get_cidreq()."&topic=".$topic."&forum=".$forum."&parentid=".$postid."'>".$lang_reply_this_message."</a>"; }
		else 
			{echo $lang_reply_this_message;}
		echo "</td></tr></table>";
		if(is_allowed_to_edit())
		{
		echo 	"<a href=\"$url_phpbb/editpost.$phpEx?post_id=".$myrowdisplaymessage["post_id"]."&topic=$topic&forum=$forum\">",
				"<img src=\"../img/edit.gif\" border=\"0\" alt=\"",$langEditDel,"\">",
				"</a>",
				"<a href=\"$url_phpbb/editpost.$phpEx?post_id=".$myrowdisplaymessage["post_id"]."&topic=$topic&forum=$forum&delete=on&submit=1\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("ConfirmYourChoice")))."')) return false;\" >",
				"<img src=\"../img/delete.gif\" border=\"0\" alt\"",$langEditDel,"\">",
				"</a>\n";
		}
		include('thread_view.inc.php');

		$sqlthreaded = "SELECT p.*, pt.post_title FROM `$tbl_posts` p, `$tbl_posts_text` pt WHERE topic_id = '$topic' AND p.post_id = pt.post_id AND p.parent_id='0' AND p.parent_id IS NOT NULL ORDER BY post_id ASC";
		$result = mysql_query($sqlthreaded, $db) OR error_die("<big>An Error Occured</big><hr>Could not connect to the Posts database. $sql");
		$numberofparents=mysql_num_rows($result);
		$myrow = mysql_fetch_array($result);
	
		// This is the Actual tree
		echo 	"<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">",
				"<tr class=\"cell_header\" align=\"left\">",
				"<td>&nbsp;<b>",
				$topic_subject,
				"</b>";
		if ($lock_state==1) echo "($lang_locked)";
		echo "&nbsp;</td></tr>";

		// if there are more than 1 parents, this means that there were already messages in the forum before the
		// switch to a threaded/flat feature was integrated. There will probably be no post_title either,
		// so if that's the case we are using re: $topic_subject as the title of the message.
		if ($numberofparents!=1)
		{
			do
			{
				echo "<tr>\n<td valign='bottom'>\n",
					 "<table cellSpacing=0 cellPadding=0 border=0><tr><td>",
					 "<img src='../img/n.gif'></td><td>&nbsp;",
					 "<a href='".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&topic=".$topic."&forum=".$forum."&postid=".$myrow[post_id]."&forumview=threaded'>";
				if (!IS_NULL($myrow["post_title"]))
				{
					echo $myrow["post_title"];
				}
				else
				{
					echo "re: ".$topic_subject;
				}
				echo "</a></td></tr></table></td>\n</tr>\n";
				display_kids($myrow[post_id]);
			} while($myrow = mysql_fetch_array($result));
		}
		else
		{
			do
			{
				echo "<tr>\n<td valign='bottom'>\n",
					 "<table cellSpacing=0 cellPadding=0 border=0><tr><td>",
					 "<img src='../img/m.gif'></td><td>",
					 "&nbsp;<a href='".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&topic=".$topic."&forum=".$forum."&postid=".$myrow[post_id]."&forumview=threaded'>";
				echo $myrow["post_title"];
				echo "</a> <font size=2>(".date("d/m/y G:i",convert_mysql_date($myrow['post_time'])).")</font></td></tr></table></td>\n</tr>\n";
				display_kids($myrow[post_id]);
			} while($myrow = mysql_fetch_array($result)); // end do
		} // end else
	} // end if ($forumview=="threaded")

	// ********** START OF FLAT VIEW CODE ***************
	if (EMPTY($forumview) or $forumview=="flat")
	{
		$result = mysql_query($sql, $db) OR error_die("<big>An Error Occured</big><hr>Could not connect to the Posts database. $sql");
		$numberofparents=mysql_num_rows($result);
		$myrow = mysql_fetch_array($result);
		do
		{
			echo "<tr><td colspan='2' bgcolor='$color1'>";
			
			if(!empty($myrow["post_title"]))
			{
				echo "<b>".$myrow["post_title"]."</b><br>";
			}
			$myrow["post_text"] = api_parse_tex($myrow["post_text"]);
			echo $myrow["post_text"];
			echo "</td></tr>";
			
			// for Dokeos resource linker	
			echo "<tr><td colspan='2'>"; 
			display_added_resources("Forum", $myrow["post_id"]);
			echo "</td></tr>";
			// end for Dokeos resource linker 	
			
			echo "<tr><td bgcolor='$color2' align='left'>".$lang_author.": ";
			echo $myrow["prenom"]." ".$myrow["nom"]." - ".$myrow["post_time"];
			echo "</td><td align='right' bgcolor='$color2'>";
			if($lock_state != 1)
			{
				echo "<a href='reply.php?topic=".$topic."&forum=".$forum."&parentid=".$myrow["post_id"]."'>".$lang_reply_this_message."</a>";
			}
			else
			{
				echo get_lang("_reply_this_message");
			}
			echo "</td></tr>";
			echo "<tr><td colspan='2' height='10'>";
			if(is_allowed_to_edit())
			{
				echo "<p>\n",
					 "<a href=\"$url_phpbb/editpost.$phpEx?post_id=$myrow[post_id]&topic=$topic&forum=$forum\">",
 					 "<img src=\"../img/edit.gif\" border=\"0\" alt=\"",get_lang("EditDel"),"\">",
 					 "</a>",
 					 "<a href=\"$url_phpbb/editpost.$phpEx?post_id=$myrow[post_id]&topic=$topic&forum=$forum&delete=on&submit=1\"  onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("ConfirmYourChoice")))."')) return false;\" >",
					 "<img src=\"../img/delete.gif\" border=\"0\" alt\"",get_lang("EditDel"),"\">",
					 "</a>\n</p>\n";
			}
			echo "</td></tr>";
		} while($myrow = mysql_fetch_array($result));
	}

// END ADDED BY UGENT, Patrick Cool, march 2004, flat/threaded view
		$count = 0;
/*		do
		{
			echo	"<tr>\n",
			"<td bgcolor=\"#e6e6e6\">\n",
			$l_author," : <b>",$myrow[prenom]," ",$myrow[nom],"</b>",
			" -- ",$l_posted," : ",$myrow[post_time],"\n",
			"</td>\n",
			"</tr>\n";

	$message = own_stripslashes($myrow[post_text]);

	// Before we insert the sig, we have to strip its HTML if HTML is disabled by the admin.
	// We do this _before_ bbencode(), otherwise we'd kill the bbcode's html.

	$sig = $posterdata[user_sig];

	if (!$allow_html)
	{
		$sig = htmlspecialchars($sig);
		$sig = preg_replace("#&lt;br&gt;#is", "<BR>", $sig);
	}

	$message = eregi_replace("\[addsig]$", "<BR>_________________<BR>" . own_stripslashes(bbencode($sig, $allow_html)), $message);

	echo	"<tr>\n",
			"<td>\n",
			$message,"\n";

// Added by Thomas 30-11-2001
// echo "<a href=\"$url_phpbb/reply.$phpEx?topic=$topic&forum=$forum&post=$myrow[post_id]&quote=1\">$langQuote</a>&nbsp;&nbsp;";

	if($is_allowedToEdit)
	{
		echo	"<p>\n",
				"<a href=\"$url_phpbb/editpost.$phpEx?post_id=$myrow[post_id]&topic=$topic&forum=$forum\">",
				"<img src=\"../img/edit.gif\" border=\"0\" alt=\"",$langEditDel,"\">",
				"<img src=\"../img/delete.gif\" border=\"0\" alt\"",$langEditDel,"\">",
				"</a>\n",
				"</p>\n";
	}

	echo	"</td>\n",
			"</tr>\n";

   $count++;

} while($myrow = mysql_fetch_array($result)); // do while */

	$sql = "UPDATE `$tbl_topics` 
	        SET topic_views = topic_views + 1 
	        WHERE topic_id = '$topic'";

	@mysql_query($sql, $db);

?>

</table>

</td>

</tr>

<?php

	// MODIFIED BY UGENT, Patrick Cool, march 2004, flat/threaded view
	if($total > $posts_per_page and $forumview!=="threaded" )
	{
		$times = 1;

		echo	"<tr align=\"right\">",
				"<td colspan=2>\n",
				$l_gotopage," ( ";

		$last_page = $start - $posts_per_page;

		if($start > 0)
		{
			echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&topic=$topic&forum=$forum&start=$last_page\">",$l_prevpage,"</a> ";
		}

		for($x = 0; $x < $total; $x += $posts_per_page)
		{
			if($times != 1)
			echo " | ";

			if($start && ($start == $x))
			{
				echo $times;
			}

			elseif($start == 0 && $x == 0)
			{
				echo "1";
			}
			else
			{
				echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&mode=viewtopic&topic=$topic&forum=$forum&start=$x\">",$times,"</a>\n";
			}

			$times++;
		}

		if(($start + $posts_per_page) < $total)
		{
			$next_page = $start + $posts_per_page;
			echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&topic=$topic&forum=$forum&start=$next_page\">",$l_nextpage,"</a>\n";
		}

		echo	" )\n";

		echo	"</td>\n",
				"</tr>\n";
	}

?>


</td>
<?
}

require('page_tail.'.$phpEx);

?>
