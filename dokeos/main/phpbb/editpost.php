<?php
/**
==============================================================================
*	This file is a code template;
*	copy the code and paste it in a new file to begin your own work.
*
*	@package dokeos.plugin
==============================================================================
*/

/***************************************************************************
                            editpost.php  -  description
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
// Modified by UGent, Patrick Cool, march 2004: added the resource linker
//----------------------------------------------------------------------

include('extention.inc');
include('functions.php');
include('config.php');
require('auth.php');

$post_id = $_REQUEST['post_id'];
$topic = $_REQUEST['topic'];
$forum= $_REQUEST['forum'];
$delete = $_REQUEST['delete'];
$submit = $_REQUEST['submit'];


// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
$_SESSION['source_type']="Forum";
include('../resourcelinker/resourcelinker.inc.php');

if ($originalresource!=="no" and !$addresources and !$submit)
	{
	edit_added_resources("Forum", $post_id);
	}

if ($addresources) // When the "Add Resource" button is clicked we store all the form data into a session
{
$form_elements= array ('subject'=>$subject, 'message'=>$message, 'stayinformed'=>$stayinformed);
$_SESSION['formelements']=$form_elements;
echo $topic_id;
header("Location: ../resourcelinker/resourcelinker.php?source_id=3&post_id=$post_id&topic=$topic&forum=$forum&md5=$md5&originalresource=no");
exit;
}

if($cancel)
{
	header("Location: viewtopic.php?topic=$topic&forum=$forum");
	exit();
}

include('page_header.php');

// getting the data for the forms from the session
if ($_SESSION['formelements'])
	{
	$form_elements=$_SESSION['formelements'];
	$subject=$form_elements["subject"];
	$message=$form_elements["message"];
	$stayinformed=$form_elements["stayinformed"];
	}
// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker


if($is_courseAdmin)
{
	$pagetitle = $lang_edit_post;
	$pagetype  = "index";

	if($submit)
	{
		/*==========================
		    FORM SUBMIT MANAGEMENT
		  ==========================*/

		$subject=$_POST['subject'];
		$message=$_POST['message'];

		$result=api_sql_query("SELECT * FROM `$tbl_posts` WHERE post_id = '$post_id'",__FILE__,__LINE__);

		if (mysql_num_rows($result) <= 0)   die($err_db_retrieve_data);

		$myrow = mysql_fetch_array($result);

		$poster_id        = $myrow[poster_id];
		$forum_id         = $myrow[forum_id];
		$topic_id         = $myrow[topic_id];
		$this_post_time   = $myrow['post_time'];
		list($day, $time) = split(" ", $myrow[post_time]);
		$posterdata       = get_userdata_from_id($poster_id, $db);
		$date             = date("Y-m-d H:i");

		$is_html_disabled = false;

		if($allow_html == 0 || isset($html) )
		{
			$message = htmlspecialchars($message);
			$is_html_disabled = true;
		}

		if($allow_bbcode == 1 && !isset($bbcode))
		{
			$message = bbencode($message, $is_html_disabled);
		}

		if(!$smile)
		{
			$message = smile($message);
		}

		// MUST do make_phpbb_clickable() (and smile()) before changing \n into <br>.
		$message = make_phpbb_clickable($message);

		//$message = str_replace("\n", "<BR>", $message);
		$message = str_replace("<w>", "<s><font color=red>", $message);
		$message = str_replace("</w>", "</font color></s>", $message);
		$message = str_replace("<r>", "<font color=#0000FF>", $message);
		$message = str_replace("</r>", "</font color>", $message);

		$message = censor_string($message, $db);

		if(!$delete)
		{
			/*--------------------------------------
			               POST UPDATE
	  		  --------------------------------------*/

			$forward = 1;
			$topic   = $topic_id;
			$forum   = $forum_id;

			$result=api_sql_query("UPDATE `$tbl_posts_text` SET post_text = '$message' , post_title = '$subject' WHERE (post_id = '$post_id')",__FILE__,__LINE__);

			// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
			if(!$delete and $originalresource!=="no")
				{
				update_added_resources("Forum", $post_id);
				}
			$_SESSION['formelements']=null;
			$_SESSION['addedresource']=null;
			$_SESSION['addedresourceid']=null;
			unset($subject);
			unset($message);
			unset($stayinformed);
			// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker

			$subject = strip_tags($subject);

			if(isset($subject) && (trim($subject) != ''))
			{
				if(!isset($notify)) $notify = 0;
				else                $notify = 1;

				/*$result = api_sql_query("UPDATE `$tbl_topics`
				                       SET topic_title = '$subject', topic_notify = '$notify'
									   WHERE topic_id = '$topic_id'",__FILE__,__LINE__);*/
			}

			echo	"<table border=\"0\" cellpadding=\"1\" ",
					"align=\"center\" valign=\"top\" width=\"$tablewidth\">\n",

					"<tr bgcolor=\"$color1\" align=\"left\">\n",
					"<td>\n",

					"<center>\n",
					$l_stored," \n",
					"<ul>\n",
					$l_click," <a href=\"viewtopic.php?topic=$topic_id&forum=$forum_id\">$l_here</a>\n",
					$l_viewmsg,"<P>$l_click <a href=\"viewforum.php?forum=$forum_id\">$l_here</a>\n",
					$l_returntopic,"\n",
					"</ul>\n",
					"</center>\n",

					"</td>\n",
					"</tr>\n",

					"</table>\n";
		}
		else
		{
			/*--------------------------------------
			              POST DELETE
	  		 --------------------------------------*/

			$now_hour         = date("H");
			$now_min          = date("i");
			list($hour, $min) = split(":", $time);

			$last_post_in_thread = get_last_post($topic_id, $db, "time_fix");

			$r = api_sql_query("DELETE FROM `$tbl_posts` WHERE post_id = '$post_id'",__FILE__,__LINE__);

			$r = api_sql_query("DELETE FROM `$tbl_posts_text` WHERE post_id = '$post_id'",__FILE__,__LINE__);

			if($last_post_in_thread == $this_post_time)
			{
				$topic_time_fixed = get_last_post($topic_id, $db, "time_fix");

				$r = api_sql_query("UPDATE `$tbl_topics` SET topic_time = '$topic_time_fixed' WHERE topic_id = '$topic_id'",__FILE__,__LINE__);
			}

			if(get_total_posts($topic_id, $db, "topic") == 0)
			{
				$r = api_sql_query("DELETE FROM `$tbl_topics` WHERE topic_id = '$topic_id'",__FILE__,__LINE__);

				$topic_removed = TRUE;
			}

			if($posterdata[user_id] != -1)
			{
					$r = api_sql_query("UPDATE `$tbl_users` SET user_posts = user_posts - 1 WHERE user_id = '".$posterdata['user_id']."'",__FILE__,__LINE__);
			}

			sync($db, $forum, 'forum');

			if(!$topic_removed)
			{
				sync($db, $topic_id, 'topic');
			}

			// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
			delete_added_resource("Forum", $topic_id);
			$_SESSION['formelements']=null;
			$_SESSION['addedresource']=null;
			$_SESSION['addedresourceid']=null;
			unset($subject);
			unset($message);
			unset($stayinformed);
			// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker

			/* CONFIRMATION MESSAGE */

			echo	"<table border=\"0\" cellpadding=\"1\" ",
					"align=\"center\" valign=\"top\" width=\"$tablewidth\">",

					"<tr bgcolor=\"",$color1,"\">",
					"<td>",

					"<center>",

					"<p>",
					$l_deleted,
					"</p>",

					"<p>",
					$l_click," <a href=\"viewforum.php?forum=$forum_id\">",$l_here,"</a> ",
					$l_returntopic,
					"</p>",

					"<p>",
					$l_click," <a href=\"index.php\">",$l_here,"</a>",
					$l_returnindex,
					"</p>",

					"</center>",

					"</td>",
					"</tr>",

					"</table>";
		}													// end post update

	}														// end submit management
	else
	{
		/*==========================
		      EDIT FORM BUILDING
		  ==========================*/

		// MODIFIED BY UGENT, Patrick Cool, march 2004, flat/threaded view
		// added pt.post_title
		$result = api_sql_query("SELECT p.*, pt.post_text, pt.post_title,
		                              u.username, u.user_id, u.user_sig,
		                              t.topic_title, t.topic_notify
		                       FROM `$tbl_posts` p, `$tbl_users` u,
		                            `$tbl_topics` t, `$tbl_posts_text` pt,
									`$tbl_forums` f
		                       WHERE (p.post_id = '$post_id')
							   AND (p.topic_id = '$topic')
							   AND (f.forum_id = '$forum')
		                       AND (pt.post_id = p.post_id)
		                       AND (p.topic_id = t.topic_id)
							   AND (p.forum_id = f.forum_id)
		                       AND (p.poster_id = u.user_id)",__FILE__,__LINE__);

		if(!$myrow = mysql_fetch_array($result))
			error_die("Error - The forum you selected does not exist. Please go back and try again.");

		if ($_GET["originalresource"]!=="no")
			{
			$message = $myrow[post_text];
			$subject = $myrow[post_title];
			}

		if(eregi("\[addsig]$", $message)) $addsig = 1;
		else $addsig = 0;

		$message = eregi_replace("\[addsig]$", "\n_________________\n" . $myrow[user_sig], $message);
		//$message = str_replace("<BR>", "\n", $message);
		$message = stripslashes($message);
		$message = desmile($message);
		$message = bbdecode($message);
		$message = undo_make_clickable($message);
		$message = undo_htmlspecialchars($message);

		// Special handling for </textarea> tags in the message, which can break the editing form..
		$message = preg_replace('#</textarea>#si', '&lt;/TEXTAREA&gt;', $message);

		list($day, $time) = split(" ", $myrow[post_time]);
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="margin:0px;">
<input type="hidden" name="md5" value="<?php echo $md5; ?>">
<table border="0" width="100%">
<tr valign="top">
<td align="center" colspan="2"><b><?php echo $pagetitle?></b></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php
		// MODIFIED BY UGENT, Patrick Cool, march 2004, flat/threaded view
		// $first_post = is_first_post($topic, $post_id, $db);

		//if($first_post)
		//{
?>
<tr valign="top">
<td width="10%" align="right" nowrap="nowrap"><?php echo $l_subject?> : </td>
<td width="90%">
<input type="text" name="subject"  size="50" maxlength="100" value="<?php echo htmlentities($subject)?>">
</td>
</tr>
<?php
		//}
		// END MODIFIED BY UGENT, Patrick Cool, march 2004, flat/threaded view
?>
<tr valign="top">
<td align="right" nowrap="nowrap"><?php echo $l_body; ?> : </td>
<td>

<?php
    api_disp_html_area('message',$message,'250px');
?>

</td>
</tr>
	<?php
	// ADDED BY UGENT, Patrick Cool, march 2004, resource linker
	echo "<tr><td valign='top' align='right'>".get_lang('AddResource')." :</td><td>";
	show_addresource_button();
	echo "</td></tr>";
   	if ($_SESSION['addedresource'])
   	echo "<tr><td valign='top' nowrap='nowrap'>".ucfirst(get_lang("ResourcesAdded"))." (".count($_SESSION['addedresource']).") :</td>";
   	echo "<td colspan='6'>";
   	echo display_resources(0);
   	echo "</td></tr>";
	// END ADDED BY UGENT, Patrick Cool, march 2004, resource linker
	?>


<tr valign="top">
<td align="right" nowrap="nowrap"><?php echo $l_delete?> : </td>
<td>
<input class="checkbox" type="checkbox" name="delete"><br>
</td>
</tr>

<tr>
<td>&nbsp;
</td>
<td>
<input type="hidden" name="post_id" value="<?php echo $post_id?>">
<?php // ADDED BY UGENT, Patrick Cool, march 2004, resource linker ?>
<input type="hidden" name="topic" value="<?php echo $topic?>">
<input type="hidden" name="forum" value="<?php echo $forum?>">
<input type="submit" name="submit" value="<?php echo $l_submit?>">
<!-- &nbsp;<input type="submit" name="cancel" value="<?php echo $l_cancelpost?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities($langConfirmYourChoice)); ?>')) return false;"> //-->
</td>
</tr>
</table>

<br>
<center>
<?php
	echo	"<a href=\"viewtopic.php?topic=$topic&forum=$forum\" target=\"_blank\">",
			"<b>",$l_topicreview,"</b>",
			"</a>";
?>
</center>

<br>
<?php
	} // end else

	include('page_tail.php');

}	// end if is allowed to edit and delete
?>
