<?php
/**
============================================================================== 
*	@package dokeos.forum
============================================================================== 
*/
// This variable is set to show the resource linker. 
// It is overwritten in builder.php which is only included when inside learnpath.
$integrationValue = 1;
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
if( isset($_REQUEST['topic']))
{
	$topic = $_REQUEST['topic'];
}
if( isset($_REQUEST['parentid']))
{
	$parentid = $_REQUEST['parentid'];
}
if( isset($_REQUEST['forum']))
{
	$forum = $_REQUEST['forum'];
}
if( isset($_REQUEST['subject']))
{
	$subject = $_REQUEST['subject'];
}
if( isset($_REQUEST['message']))
{
	$message = $_REQUEST['message'];
}
if( isset($_REQUEST['quote']))
{
	$quote = $_REQUEST['quote'];
}
if( isset($_REQUEST['submit']))
{
	$submit = $_REQUEST['submit'];
}


/***************************************************************************
                            reply.php  -  description
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

// =============== RESOURCE LINKER SECTION ==============================
// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
$_SESSION['source_type']="Forum"; 
include('../resourcelinker/resourcelinker.inc.php');

if ($addresources) // When the "Add Resource" button is clicked we store all the form data into a session
	{
	$form_elements= array ('subject'=>$subject, 'message'=>$message, 'stayinformed'=>$stayinformed);
	$_SESSION['formelements']=$form_elements; 
	header("Location: ../resourcelinker/resourcelinker.php?source_id=6&topic=$topic&forum=$forum&parentid=$parentid");
	exit; 
	}
$_SESSION['origintoolurl']=$_SERVER['REQUEST_URI']; 
// getting the data for the forms from the session
if ($_SESSION['formelements'])
	{
	$form_elements=$_SESSION['formelements']; 
	$subject=$form_elements["subject"];
	$message=$form_elements["message"];
	$stayinformed=$form_elements["stayinformed"];
	}
// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker
// =============== END RESOURCE LINKER SECTION ==============================


if($cancel)
{
	header("Location: viewtopic.php?topic=$topic&forum=$forum");
	exit();
}

$pagetitle = "Post Reply";
$pagetype = "reply";

if ($post_id)
{
	// We have a post id, so include that in the checks..
	$sql = "SELECT f.forum_type, f.forum_name, f.forum_access ".
	       "FROM `$tbl_forums` f, `$tbl_topics` t, `$tbl_posts` p ".
	       "WHERE (f.forum_id = '$forum') ".
	       "AND (t.topic_id = '$topic') ".
	       "AND (p.post_id = '$post_id') ".
	       "AND (t.forum_id = f.forum_id) ".
	       "AND (p.forum_id = f.forum_id) ".
	       "AND (p.topic_id = t.topic_id)";
}
else
{
	// No post id, just check forum and topic.
	$sql = "SELECT f.forum_type, f.forum_name, f.forum_access ".
	       "FROM `$tbl_forums` f, `$tbl_topics` t ".
	       "WHERE (f.forum_id = '$forum') AND (t.topic_id = '$topic') AND (t.forum_id = f.forum_id)";
}

$result=api_sql_query($sql,__FILE__,__LINE__);

if (!$myrow = mysql_fetch_array($result,MYSQL_ASSOC))
{
	error_die("The forum/topic you selected does not exist.");
}

$forum_name   = $myrow["forum_name"];
$forum_access = $myrow["forum_access"];
$forum_type   = $myrow["forum_type"];
$forum_id     = $forum;

if(is_locked($topic, $db))
{
	error_die ($l_nopostlock);
}

if(!does_exists($forum, $db, "forum") || !does_exists($topic, $db, "topic"))
{
	error_die("The forum or topic you are attempting to post to does not exist. Please try again.");
}

include('page_header.php');

if($submit)
{
	if(trim($message) == '')
	{
		error_die($l_emptymsg);
	}

	if (!$user_logged_in)
	{
		if($username == '' && $password == '' && $forum_access == 2)
		{
			// Not logged in, and username and password are empty and forum_access is 2 (anon posting allowed)
			$userdata = array("user_id" => -1);
		}
		else if($username == '' || $password == '')
		{
			// no valid session, need to check user/pass.
			error_die($l_userpass);
		}

		if($userdata[user_level] == -1)
		{
			error_die($l_userremoved);
		}

		if($userdata[user_id] != -1)
		{
			$md_pass = md5($password);
			$userdata = get_userdata($username, $db);

			if($md_pass != $userdata["user_password"])
			{
				error_die($l_wrongpass);
			}
		}

		if($forum_access == 3 && $userdata[user_level] < 2)
		{
			error_die($l_nopost);
		}

		if(is_banned($userdata[user_id], "username", $db))
		{
			error_die($l_banned);
		}

		if($userdata[user_id] != -1)
		{
			 // You've entered your username and password, so we log you in.
			 $sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
			 set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
		}
	}
	else
	{
		if($forum_access == 3 && $userdata[user_level] < 2)
		{
			error_die($l_nopost);
		}
	}

	// Either valid user/pass, or valid session. continue with post.. but first:
	// Check that, if this is a private forum, the current user can post here.
	if ($forum_type == 1)
	{
		if (!check_priv_forum_auth($userdata[user_id], $forum, TRUE, $db))
		{
			error_die("$l_privateforum $l_nopost");
		}
	}

	$poster_ip = $REMOTE_ADDR;

	$is_html_disabled = false;

	if($allow_html == 0 || isset($html))
	{
		$message          = htmlspecialchars($message);
		$is_html_disabled = true;

		if ($quote)
		{
			$edit_by = get_syslang_string($sys_lang, "l_editedby");

			// If it's been edited more than once, there might be old "edited by" strings with
			// escaped HTML code in them. We want to fix this up right here:
			$message = preg_replace("#&lt;font\ size\=-1&gt;\[\ $edit_by(.*?)\ \]&lt;/font&gt;#si", '<small>[ ' . $edit_by . '\1 ]</small>', $message);
		}
	}

	if($allow_bbcode == 1 && !isset($bbcode))
	{
		$message = bbencode($message, $is_html_disabled);
	}

	// MUST do make_phpbb_clickable() and smile() before changing \n into <br>.
	$message = make_phpbb_clickable($message);
	if(!$smile)
	{
		$message = smile($message);
	}

	//$message = str_replace("\n", "<BR>", $message);
	$message = str_replace("<w>", "<s><font color=red>", $message);
	$message = str_replace("</w>", "</font color></s>", $message);
	$message = str_replace("<r>", "<font color=#0000FF>", $message);
	$message = str_replace("</r>", "</font color>", $message);

	$message = censor_string($message, $db);
	$message = addslashes($message);
	$time = date("Y-m-d H:i");


	// ADDED BY Thomas 20.2.2002

   $nom    = addslashes($nom);
   $prenom = addslashes($prenom);

   // END ADDED BY THOMAS

	//to prevent [addsig] from getting in the way, let's put the sig insert down here.
	if($sig && $userdata[user_id] != -1)
	{
		$message .= "\n[addsig]";
	}

	// ADDED BY UGENT, Patrick Cool, february 2004,  Topic notification
	// Checking if the user wants to be informed. If the checkbox is checked, the users want to be informed
	// and the $topic_notify = 1 (0=do NOT receive topic notification, 1=DO receive topic notification)
	if ($stayinformed=="checked")
		{
		$topic_notify=1;
		}
	else
		{
		$topic_notify=0;
		}
	// END ADDED BY UGENT, Patrick Cool, february 2004,  Topic notification

	// MODIFIED BY UGENT, Patrick Cool, february 2004,  Topic notification and threaded view
	// added field topic_notify and its value = the value of the form stayinformed
	// added field parent_id
	$sql = "INSERT INTO `$tbl_posts` ".
	       "(topic_id, parent_id, forum_id, poster_id, post_time, poster_ip, nom, prenom, topic_notify)".
	       "VALUES ('$topic', '$parentid', '$forum', '$userdata[user_id]','$time', '$poster_ip', '$nom', '$prenom', '$topic_notify')";
	// END MODIFIED BY UGENT, Patrick Cool, february 2004,  Topic notification
	$result=api_sql_query($sql,__FILE__,__LINE__);

	// ADDED BY UGENT, Patrick Cool, february 2004,  Topic notification
	// sending the mails to the people who want to receive topic notification
	include('../inc/phpbb_topic_notification.inc.php');

	$this_post = mysql_insert_id();
	
	// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
	store_resources($_SESSION['source_type'], $this_post);
	$_SESSION['formelements']=null;
	$_SESSION['addedresource']=null;
	$_SESSION['addedresourceid']=null;
	// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker
	
	if($this_post)
	{
		$sql = "INSERT INTO `$tbl_posts_text` (post_id, post_text, post_title) VALUES ('$this_post', '$message', '".addslashes($subject)."')";

		$result=api_sql_query($sql,__FILE__,__LINE__);
	}

	$sql = "UPDATE `$tbl_topics` ".
	       "SET topic_replies = topic_replies+1, ".
	       "topic_last_post_id = '$this_post', ".
	       "topic_time = '$time' ".
	       "WHERE topic_id = '$topic'";

	$result=api_sql_query($sql,__FILE__,__LINE__);

	if($userdata["user_id"] != -1)
	{
		$sql = "UPDATE `$tbl_users` SET user_posts=user_posts+1 WHERE (user_id = '".$userdata['user_id']."')";
		$result=api_sql_query($sql,__FILE__,__LINE__);
	}

	$sql = "UPDATE `$tbl_forums` ".
	       "SET forum_posts = forum_posts+1, forum_last_post_id = '$this_post' ".
	       "WHERE forum_id = '$forum'";

	$result=api_sql_query($sql,__FILE__,__LINE__);

	$sql = "SELECT t.topic_notify, u.user_email, u.username, u.user_id
	        FROM `$tbl_topics` t, `$tbl_users` u
			WHERE t.topic_id = '$topic' AND t.topic_poster = u.user_id";

	$result=api_sql_query($sql,__FILE__,__LINE__);

	$m = mysql_fetch_array($result,MYSQL_ASSOC);

	if($m["topic_notify"] == 1 && $m["user_id"] != $userdata["user_id"])
	{
		// We have to get the mail body and subject line in the board default language!
		$subject = get_syslang_string($sys_lang, "l_notifysubj");
		$message = get_syslang_string($sys_lang, "l_notifybody");
		eval("\$message =\"$message\";");
		@api_send_mail($m[user_email], $subject, $message, "From: $email_from\r\nX-Mailer: phpBB $phpbbversion");
	}


	$total_forum = get_total_topics($forum, $db);
	$total_topic = get_total_posts($topic, $db, "topic")-1;
	// Subtract 1 because we want the nr of replies, not the nr of posts.

	$forward = 1;

	echo	"<br>\n",
			"<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" align=\"center\" valign=\"top\" width=\"$tablewidth\">\n\t",
			"<tr>\n\t\t",
			"<td  bgcolor=\"$table_bgcolor\">\n\t\t\t",
			"<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">\n\t\t\t\t",
			"<tr bgcolor=\"$color1\">\n\t\t\t\t\t",
			"<td>\n\t\t\t\t\t\t",
			"<center>\n\t\t\t\t\t\t\t",
			$l_stored,"\n\t\t\t\t\t\t\t\t",
			"<ul>\n\t\t\t\t\t\t\t\t\t",
			"$l_click <a href=\"viewtopic.php?topic=",$topic,"&forum=",$forum,"&",$total_topic,"\">$l_here</a> ",$l_viewmsg,"\n",
			"<p>\n",
			$l_click, " <a href=\"viewforum.php?forum=$forum&$total_forum\">$l_here</a>\n", "$l_returntopic\n\t\t\t\t\t\t\t\t\t",
			"</ul>\n\t\t\t\t\t\t\t\t",
			"</center>\n\t\t\t\t\t\t\t\t",
			"</td>\n\t\t\t\t\t\t\t",
			"</tr>\n\t\t\t\t\t\t",
			"</table>\n\t\t",
			"</td>\n\t",
			"</tr>\n",
			"</table>\n",
			"<br>\n";
}
else
{
	// Private forum logic here.

	if(($forum_type == 1) && !$user_logged_in && !$logging_in)
	{
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<table border="0" cellpadding="1" cellspacing="0" align="center" valign="top" width="<?php echo $tablewidth?>">
<tr>
<td bgcolor="<?php echo $table_bgcolor?>">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
<tr bgcolor="<?php echo $color1?>" align="left">
<td align="center"><?php echo $l_private?></td>
</tr>
<tr bgcolor="<?php echo $color2?>" align="left">
<td align="center">

</td>
</tr>
<tr bgcolor="<?php echo $color1?>" align="left">
<td align="center">
<input type="hidden" name="forum" value="<?php echo $forum?>">
<input type="hidden" name="topic" value="<?php echo $topic?>">
<input type="hidden" name="post" value="<?php echo $post?>">
<input type="hidden" name="quote" value="<?php echo $quote?>">
<input type="submit" name="logging_in" value="<?php echo $l_enter?>">
</td>
</tr>
</table>
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
				error_die($l_userpass);
			}
			if (!check_username($username, $db))
			{
				error_die($l_nouser);
			}
			if (!check_user_pw($username, $password, $db))
			{
				error_die($l_wrongpass);
			}

			/* if we get here, user has entered a valid username and password combination. */
			$userdata = get_userdata($username, $db);
			$sessid   = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
			set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
		}

		// ADDED BY CLAROLINE: exclude non identified visitors
		if (!$_uid AND !$fakeUid)
		{
			echo	"<center>",
					"<p>",
					$langLoginBeforePost1,"<br>",
					$langLoginBeforePost2,
					"<a href=../../index.php>",$langLoginBeforePost3,"</a>",
					"</p>",
					"</center>";
			exit();
		}

		if ($forum_type == 1)
		{
			// To get here, we have a logged-in user. So, check whether that user is allowed to view
			// this private forum.
			if (!check_priv_forum_auth($userdata[user_id], $forum, TRUE, $db))
			{
				error_die("$l_privateforum $l_nopost");
			}
			// Ok, looks like we're good.
		}
	}


?>

<form action="<?php echo $_SERVER['PHP_SELF'];?>?parentid=<?php echo $parentid; ?>" method="post" style="margin:0px;">
<table border="0" width="100%">
  <TR VALIGN="TOP">
    <TD ALIGN="RIGHT" width="10%" nowrap="nowrap"> <?php echo $l_subject?> : </TD>
    <TD width="90%">
      <?php
	  // There is a difference between clicking the reply on the top of the page and clicking the
	  // the "reply to this topic" sentence. The first adds a general reply of the first level
	  // (thus a reply of level 1 where, level 0 = the initial message), while the second adds
	  // a reply to a reply
	  //here we are getting the titel to fill the title box with something like re: parent_id_title
	if (!empty($parentid))
	  	// we have a specific reply where we are replying to. Thus a reply to a reply
		{
		$sql = "SELECT  post_title FROM `$tbl_posts_text` WHERE post_id=".$parentid ;
		$result=api_sql_query($sql,__FILE__,__LINE__);
		$myrow = mysql_fetch_array($result);
		$parent_title=$myrow["post_title"];
		}
	else
		// we do not have a reply to a reply. This is a reply to the thread, so the $parent_id
		// should contain the topic_id of the first message of this thread
		{
		// grabbing the topic_id of the first message of this thread. It would be easier if the table
		// $tbl_topics contained the topic_id of the first message
		$sqlselectroot = "SELECT post_id FROM `$tbl_posts` WHERE topic_id=".$topic." ORDER BY post_id ASC" ;
		$resultselectroot=api_sql_query($sqlselectroot,__FILE__,__LINE__);
		$myrowselectroot = mysql_fetch_array($resultselectroot);
		$parentid=$myrowselectroot[post_id];
		// grabbing the title of this thread
	  	$sql2 = "SELECT  topic_title FROM `$tbl_topics` WHERE topic_id=".$topic ;
		$result2=api_sql_query($sql2,__FILE__,__LINE__);
		$myrow2 = mysql_fetch_array($result2);
		$parent_title=$myrow2["topic_title"];
		}
	  ?>
      <INPUT NAME="subject" TYPE="TEXT" value="<?php 
	  if (isset($subject)) 
	  echo $subject;  
	  else
	  echo "re: ".htmlentities($parent_title);?> " SIZE="50" MAXLENGTH="100">
    </TD>
  </TR>

<tr valign="top">
<td align="right" nowrap="nowrap"><?php echo $l_body?> :
<?php
	if($quote)
	{
		$r=api_sql_query("SELECT pt.post_text, p.post_time, u.username
                             FROM `$tbl_posts` p, `$tbl_users` u, `$tbl_posts_text` pt
                             WHERE p.post_id   = '$post'
                             AND   p.poster_id = u.user_id
                             AND   pt.post_id  = p.post_id",__FILE__,__LINE__);

		$m                = mysql_fetch_array($r,MYSQL_ASSOC);
		$text             = desmile($m[post_text]);
		$text             = str_replace("<BR>", "\n", $text);
		$text             = stripslashes($text);
		$text             = bbdecode($text);
		$text             = undo_make_clickable($text);
		$text             = str_replace("[addsig]", "", $text);

		$syslang_quotemsg = get_syslang_string($sys_lang, "l_quotemsg");

		eval("\$reply = \"$syslang_quotemsg\";");
	}
?>
</td>
<td>

<?php
    api_disp_html_area('message',$message,'250px');
?>

</td>
</tr>
	<?php
	// ADDED BY UGENT, Patrick Cool, february 2004, topic notification
	echo "<tr><td>&nbsp;</td><td>";
	echo "<input class=\"checkbox\" name='stayinformed' type='checkbox' id='stayinformed' value='checked'";
	// MODIFIED BY UGENT, Patrick Cool, march 2004, resource linker
	if ($stayinformed) { echo " checked "; }
	echo "> ";
	echo $lang_mail_notification_yesiwant;
	echo "</td></tr>";
	// END ADDED BY UGENT, Patrick Cool, february 2004, topic notification
	?>
	<?php
	// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
	// MODIFIED BY Istvan Mandak, 2005.02
	if(isset($integrationValue))
	{
		if ($integrationValue)
		{
			echo "<tr><td valign='top' align='right' nowrap='nowrap'>".get_lang('AddResource').":</td><td>";
			show_addresource_button();
			echo "</td></tr>";
		 	if ($_SESSION['addedresource'])
		 	echo "<tr><td valign='top' nowrap='nowrap'>".ucfirst(get_lang("ResourcesAdded"))." (".count($_SESSION['addedresource']).") :</td>";
		 	echo "<td colspan='6'>";
		 	echo display_resources(0);
		 	echo "</td></tr>";
   	}
  }
  // END MODIFIED BY Istvan Mandak
	// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker
	?>
<tr>
<td>
</td>
<td>
<input type="hidden" name="forum" value="<?php echo $forum?>">
<input type="hidden" name="topic" value="<?php echo $topic?>">
<input type="hidden" name="quote" value="<?php echo $quote?>">
<input type="submit" name="submit" value="<?php echo $l_submit?>">
<!-- &nbsp;<input type="submit" name="cancel" value="<?php echo $l_cancelpost?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities($langConfirmYourChoice)); ?>')) return false;"> //-->
</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
<?php
	// Topic review
	echo    "<br>",
            "<center>",
            "<a href=\"viewtopic.php?topic=$topic&forum=$forum\" target=\"_blank\">",
            "<b>$l_topicreview</b>",
            "</a>",
            "</center>",
            "<br>";

}
require('page_tail.php');
?>
