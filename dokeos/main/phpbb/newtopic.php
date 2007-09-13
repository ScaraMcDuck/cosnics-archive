<?php
// $Id$
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
* This file is based on newtopic.php of phpBB1.4,
* with many modifications.
*
* @author Istvan Mandak, February 2005
* @author Patrick Cool, march 2004: added the resource linker 
* @package dokeos.forum
==============================================================================
*/

$md5 = $_REQUEST['md5'];
$subject = $_REQUEST['subject'];
$message = $_REQUEST['message'];
$forum = $_REQUEST['forum'];
$submit = $_REQUEST['submit'];
// This variable is set to show the resource linker. 
// It is overwritten in builder.php which is only included when inside learnpath.
$integrationValue = 1;
// modified by Istvan Mandak, 2005.02
if (isset ($_GET["lp"]))
{
	if (strcmp($_GET["lp"], "true") == 0)
	{
		require_once "builder.php";
	}
}
else
{
	if (isset ($_REQUEST["lp"]))
	{
		if (strcmp($_REQUEST["lp"], "true") == 0)
		{
			require_once "builder.php";
		}
	}
}
// end of the included section

include ('extention.inc');
api_use_lang_files("phpbb");

include ('functions.php');
include ('config.php');
require ('auth.php');

// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
$_SESSION['source_type'] = "Forum";
include ('../resourcelinker/resourcelinker.inc.php');

if ($addresources) // When the "Add Resource" button is clicked we store all the form data into a session
{
	$form_elements = array ('subject' => $subject, 'message' => $message, 'stayinformed' => $stayinformed);
	$_SESSION['formelements'] = $form_elements;

	header("Location: ../resourcelinker/resourcelinker.php?source_id=2&source_forum=$forum&md5=$md5");
	exit;
}

// getting the data for the forms from the session
if ($_SESSION['formelements'])
{
	$form_elements = $_SESSION['formelements'];
	$subject = $form_elements["subject"];
	$message = $form_elements["message"];
	$stayinformed = $form_elements["stayinformed"];
}
// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker

if ($cancel)
{
	header("Location: viewforum.php?forum=$forum");
	exit ();
}

$pagetitle = $lang_new_topic;
$pagetype = "newtopic";

$sql_query = "
SELECT 	`f`.`forum_name` forum_name,
		`f`.`forum_access` forum_access,
		`f`.`forum_type` forum_type,
		`g`.`id`	`idGroup`,
		`g`.`name` 	`nameGroup`
	FROM `".$tbl_forums."` `f`
	LEFT JOIN `".$tbl_student_group."` `g`
		ON `f`.`forum_id` = `g`.`forum_id`
	WHERE `f`.`forum_id` = '".$forum."'";

if (!$result = mysql_query($sql_query, $db))
	error_die("Can't get forum data.");

if (!$myrow = mysql_fetch_array($result, MYSQL_ASSOC))
	error_die("The forum you are attempting to post to does not exist. Please try again.");

$forum_name = $myrow["forum_name"];
$forum_access = $myrow["forum_access"];
$forum_type = $myrow["forum_type"];
$forum_groupId = $myrow["idGroup"];
$forum_groupname = $myrow["nameGroup"];
$forum_id = $forum;

if (is_null($myrow["idGroup"]) || $myrow["idGroup"] == $_gid)
{
	if(!is_null($myrow["idGroup"]))
	{
		require_once(api_get_library_path().'/groupmanager.lib.php');
		if( ! GroupManager::user_has_access($_uid,$_gid,GROUP_TOOL_FORUM) )
		{
			api_not_allowed();	
		}
	}
	// Form for new topic submitted
	if ($submit)
	{
		$subject = $_POST['subject'];
		$message = $_POST['message'];

		$subject = strip_tags($subject);
		if (trim($message) == '' || trim($subject) == '')
		{
			error_die($l_emptymsg);
		}
		if (!$user_logged_in)
		{
			if ($username == '' && $password == '' && $forum_access == 2)
			{
				// Not logged in, and username and password are empty and forum_access is 2 (anon posting allowed)
				$userdata = array ("user_id" => -1);
			}
			else
			{
				// no valid session, need to check user/pass.
				if ($username == '' || $password == '')
				{
					error_die("$l_userpass $l_tryagain");
				}
				$md_pass = md5($password);
				$userdata = get_userdata($username, $db);
				if ($userdata[user_level] == -1)
				{
					error_die($l_userremoved);
				}
				if ($md_pass != $userdata["user_password"])
				{
					error_die("$l_wrongpass $l_tryagain");
				}
				if ($forum_access == 3 && $userdata[user_level] < 2)
				{
					error_die($l_nopost);
				}
				if (is_banned($userdata[user_id], "username", $db))
				{
					error_die($l_banned);
				}
			}
			if ($userdata[user_id] != -1)
			{
				// You've entered your password and username, we log you in.
				$sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
				set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
			}
		}
		else
		{
			if ($forum_access == 3 && $userdata[user_level] < 2)
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

		$is_html_disabled = false;
		if ($allow_html == 0 || isset ($html))
		{
			$message = htmlspecialchars($message);
			$is_html_disabled = true;
		}

		if ($allow_bbcode == 1 && !($_POST['bbcode']))
			$message = bbencode($message, $is_html_disabled);

		// MUST do make_phpbb_clickable() and smile() before changing \n into <br>.
		$message = make_phpbb_clickable($message);
		if (!$smile)
		{
			$message = smile($message);
		}
		//$message = str_replace("\n", "<BR>", $message);
		$message = str_replace("<w>", "<s><font color=red>", $message);
		$message = str_replace("</w>", "</font color></s>", $message);
		$message = str_replace("<r>", "<font color=#0000FF>", $message);
		$message = str_replace("</r>", "</font color>", $message);

		$message = censor_string($message, $db);
		$subject = strip_tags($subject);
		$subject = censor_string($subject, $db);
		$poster_ip = $REMOTE_ADDR;
		$time = date("Y-m-d H:i");

		// ADDED BY Thomas 20.2.2002

		$nom = addslashes($nom);
		$prenom = addslashes($prenom);

		// END ADDED BY THOMAS

		//to prevent [addsig] from getting in the way, let's put the sig insert down here.
		if ($sig && $userdata["user_id"] != -1)
		{
			$message .= "\n[addsig]";
		}

		// ADDED BY UGENT, Patrick Cool, february 2004,  Topic notification
		// Checking if the user wants to be informed. If the checkbox is checked, the users want to be informed
		// and the $topic_notify = 1 (0=do NOT receive topic notification, 1=DO receive topic notification)
		if ($stayinformed == "checked")
		{
			$topic_notify = 1;
		}
		else
		{
			$topic_notify = 0;
		}
		// END ADDED BY UGENT, Patrick Cool, february 2004,  Topic notification

		// MODIFIED BY UGENT, Patrick Cool, february 2004,  Topic notification
		// added field topic_notify and its value = the value of the form stayinformed
		$sql = "INSERT INTO `".$tbl_topics."` (topic_title, topic_poster, forum_id, topic_time, topic_notify, nom, prenom)
			VALUES ('".$subject."', '".$userdata["user_id"]."', '".$forum."', '".$time."', ".$topic_notify.", '".$nom."', '".$prenom."')";
		// END MODIFIED BY UGENT, Patrick Cool, february 2004,  Topic notification

		if (!$result = mysql_query($sql, $db))
		{
			error_die("Couldn't enter topic in database.");
		}
		$topic_id = mysql_insert_id();

		// ADDED BY UGENT, Toon Van Hoecke, february 2004, What's new
		$wn_message = $forum_groupId == NULL ? "ForumTopicAdded" : "GroupForumTopicAdded";
		api_item_property_update($_course, TOOL_BB_FORUM, $topic_id, $wn_message, $_uid, $forum_groupId);
		// END ADDED BY UGENT, Toon Van Hoecke, february 2004, What's new

		// MODIFIED BY UGENT, Patrick Cool, february 2004,  Topic notification
		// added field topic_notify and its value = the value of the form stayinformed
		$sql = "INSERT INTO `".$tbl_posts."`
					(topic_id, forum_id, poster_id, post_time, poster_ip, nom, prenom, topic_notify)
					VALUES ('".$topic_id."', '".$forum."', '".$userdata[user_id]."', '".$time."', '".$poster_ip."', '".$nom."', '".$prenom."', '".$topic_notify."')";
		// END MODIFIED BY UGENT, Patrick Cool, february 2004,  Topic notification

		if (!$result = mysql_query($sql, $db))
		{
			error_die("Couldn't enter post in datbase.");
		}
		else
		{
			$post_id = mysql_insert_id();

			// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
			store_resources($_SESSION['source_type'], $post_id);
			$_SESSION['formelements'] = null;
			$_SESSION['addedresource'] = null;
			$_SESSION['addedresourceid'] = null;
			// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker

			if ($post_id)
			{
				$sql = "INSERT INTO `".$tbl_posts_text."` (post_id, post_text, post_title) values ('".$post_id."', '".$message."', '".$subject."')";
				if (!$result = mysql_query($sql, $db))
				{
					error_die("Could not enter post text!");
				}

				// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
				unset ($subject);
				unset ($message);
				unset ($stayinformed);
				// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker

				$sql = "UPDATE `$tbl_topics` SET topic_last_post_id = '$post_id' WHERE topic_id = '$topic_id'";

				if (!$result = mysql_query($sql, $db))
				{
					error_die("Could not update topics table!");
				}
			}
		}

		if ($userdata[user_id] != -1)
		{
			$sql = "UPDATE `$tbl_users` SET user_posts=user_posts+1 WHERE (user_id = '".$userdata['user_id']."')";
			$result = mysql_query($sql, $db);
			if (!$result)
			{
				error_die("Couldn't update users post count.");
			}
		}
		$sql = "UPDATE `$tbl_forums` SET forum_posts = forum_posts+1, forum_topics = forum_topics+1, forum_last_post_id = '$post_id' WHERE forum_id = '$forum'";
		$result = mysql_query($sql, $db);
		if (!$result)
		{
			error_die("Couldn't update forums post count.");
		}
		$topic = $topic_id;
		$total_forum = get_total_topics($forum, $db);
		$total_topic = get_total_posts($topic, $db, "topic") - 1;
		// Subtract 1 because we want the nr of replies, not the nr of posts.
		$forward = 1;
		include ('page_header.php');
		echo "<br>", "<TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\">", "<TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\">", "<TD>", "<center>", $l_stored, "<p>", $l_click, " <a href=\"viewtopic.$phpEx?topic=$topic_id&forum=$forum&$total_topic\">", $l_here, "</a> ", $l_viewmsg, "<p>", $l_click, " <a href=\"viewforum.$phpEx?forum=$forum_id&$total_forum\">", $l_here, "</a> ", $l_returntopic, "</center>", "</td>", "</tr>", "</table>";
	}
	else
	{
		include ('page_header.php');

		// ADDED BY CLAROLINE: exclude non identified visitors
		if (!$_uid AND !$fakeUid)
		{
			echo "<center><br><br><font face=\"arial, helvetica\" size=2>$langLoginBeforePost1<br>
						$langLoginBeforePost2<a href=../../index.php>$langLoginBeforePost3.</a></center>";
			exit ();
		}

		// END ADDED BY CLAROLINE exclude visitors unidentified
?>

<p align="center"><b><?php echo $pagetitle?></b></p>

<FORM ACTION="<?php echo $_SERVER['PHP_SELF']?>" METHOD="POST" style="margin:0px;">
<input type="hidden" name="md5" value="<?php echo $md5; ?>">
<TABLE BORDER="0" width="100%">
	<TR VALIGN="TOP">
		<TD ALIGN="RIGHT">
			<?php echo $l_subject?> :
		</TD>
		<TD>
			<?php

?>


			<INPUT NAME="subject" TYPE="TEXT" value="<?php if (isset($subject)) echo htmlentities($subject); ?>" SIZE="50" MAXLENGTH="100">
		</TD>
	</TR>
	<TR VALIGN="TOP">
		<TD ALIGN="RIGHT">
			<?php echo $l_body?> :
			<br>
			<br>
		</TD>
		<TD>

<?php

		api_disp_html_area('message', $message, '250px');
?>

		</TD>
	</TR>
	<?php

		// ADDED BY UGENT, Patrick Cool, february 2004, topic notification
		echo "<tr><td></td><td>";
		echo "<input class=\"checkbox\" name='stayinformed' type='checkbox' id='stayinformed' value='checked'";
		// MODIFIED BY UGENT, Patrick Cool, march 2004, resource linker
		if ($stayinformed)
		{
			echo " checked ";
		}
		echo "> ";
		echo $lang_mail_notification_yesiwant;
		echo "</td></tr>";
		// END ADDED BY UGENT, Patrick Cool, february 2004, topic notification
?>
	<?php

		// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
		// MODIFIED BY Istvan Mandak, 2005.02
		if (isset ($integrationValue))
		{
			if ($integrationValue)
			{
				echo "<tr><td valign='top' align='right' nowrap='nowrap'>".get_lang('AddResource')." :</td><td>";
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
	<TR>
		<TD>
		</TD>
		<TD>
			<INPUT TYPE="HIDDEN" NAME="forum" VALUE="<?php echo $forum?>">
			<INPUT TYPE="SUBMIT" NAME="submit" VALUE="<?php echo $l_submit?>">
			<!-- &nbsp;<INPUT TYPE="SUBMIT" NAME="cancel" VALUE="<?php echo $l_cancelpost?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities($langConfirmYourChoice)); ?>')) return false;"> //-->
		</TD>
	</TR>
</TABLE>
</FORM>

<?php

	}
}
else
{
	header("Location: index.php");
	exit ();
}
require ('page_tail.php');
?>