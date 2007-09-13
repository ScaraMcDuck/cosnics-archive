<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	
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
*	@package dokeos.forum_admin
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


	
/*
==============================================================================
		INIT SECTION
==============================================================================
*/
api_use_lang_files("forum_admin");
include("../inc/claro_init_global.inc.php");
api_protect_course_script();

// Roles and rights system
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_BB_FORUM);
$is_allowed = RolesRights::is_allowed_which_rights($role_id, $location_id);

//block users without view right
RolesRights::protect_location($role_id, $location_id);

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
$langYouCannotDelCatOfGroupsForums =" you can't delete category of group's forum's";


$TBL_FORUMS      = $_course['dbNameGlu']."bb_forums";
$TBL_CATAGORIES  = $_course['dbNameGlu']."bb_categories";
$TBL_USERS       = $_course['dbNameGlu']."bb_users";
$TBL_FORUM_MODS  = $_course['dbNameGlu']."bb_forum_mods";
$TBL_FORUMTOPICS = $_course['dbNameGlu']."bb_forumtopics";

$categories = $_REQUEST['categories'];
$add_forum_category = $_REQUEST['add_forum_category'];
$forumgo = $_REQUEST['forumgo'];
$cat_id = $_REQUEST['cat_id'];
$cat_title = $_REQUEST['cat_title'];
$ctg = $_REQUEST['ctg'];
$forumcatedit = $_REQUEST['forumcatedit'];
$forumcatdel = $_REQUEST['forumcatdel'];
$forumcatsave = $_REQUEST['forumcatsave'];
$forumgoadd = $_REQUEST['forumgoadd'];
$forum_id = $_REQUEST['forum_id'];
$forum_name = $_REQUEST['forum_name'];
$forum_desc = $_REQUEST['forum_desc'];
$forumgoedit = $_REQUEST['forumgoedit'];
$forumgodel = $_REQUEST['forumgodel'];
$ok = $_REQUEST['ok'];
$forumgosave = $_REQUEST['forumgosave'];

$ctg=stripslashes($_GET['ctg']);

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/ 
$nameTools = get_lang("Organisation");
$interbredcrump[]= array ("url"=>"../phpbb/index.php", "name"=> get_lang("Forums"));
Display::display_header($nameTools,"For");

if ($is_allowed[EDIT_RIGHT] || $is_allowed[ADD_RIGHT] || $is_allowed[DELETE_RIGHT] )
{

	/*==================================
	  GO TO FORUMS LIST OF THIS CATEGORY
	  ==================================*/

	if($forumgo)
	{
		echo	"<h3>",
				$nameTools,"</h3>";


		echo	"<b>",get_lang("ForCat")," ",$ctg,"</b><br>",
				"<div align=\"right\">",
				"<a href=\"".$_SERVER['PHP_SELF']."?forumadmin=yes\">", get_lang("BackCat"), "</a>",
				"<form action=\"forum_admin.php?forumgoadd=yes&ctg=$ctg&cat_id=$cat_id\" method=post>",
				"</div>",

				"<table border=0 width=\"100%\" cellpadding=4 cellspacing=2>",

				"<tr bgcolor=\"#E6E6E6\">\n",
				"<td>",get_lang("ForName"),"</td>\n",
				"<td>",get_lang("Description"),"</td>\n",
				"<td align=\"center\">",get_lang("Modify"),"</td>\n";
				
		if ($is_allowed[DELETE_RIGHT]) echo "<td align=\"center\">" . get_lang("Delete") . "</td>\n";
		
		echo "</tr>\n";

		$result = mysql_query("SELECT forum_id, forum_name, forum_desc, forum_access,
		                              forum_moderator, forum_type from `$TBL_FORUMS`
		                       WHERE cat_id='$cat_id'");

		$i=0;

		while( list($forum_id, $forum_name,
					$forum_desc, $forum_access,
					$forum_moderator, $forum_type)
					= mysql_fetch_row($result))
		{
			echo	"<tr>\n",

					"<td valign=top>",$forum_name,"</td>\n",
					"<td valign=top>",$forum_desc,"&nbsp;</td>\n",
					"<td valign=top align=\"center\">\n",
					"<a href=forum_admin.php?forumgoedit=yes&forum_id=$forum_id&ctg=$ctg&cat_id=$cat_id>",
					"<img src=\"../img/edit.gif\" alt=\"",get_lang("Modify"),"\" border=\"0\">",
					"</a>",
					"</td>\n";
					
					if ($cat_id!=1 && $is_allowed[DELETE_RIGHT])
					{
						echo "<td align=\"center\">",
						"<a href=\"forum_admin.php?forumgodel=yes&forum_id=$forum_id&cat_id=$cat_id&ctg=$ctg&ok=0\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("ConfirmYourChoice")))."')) return false;\">",
						"<img src=\"../img/delete.gif\" alt=\"",get_lang("Delete"),"\" border=\"0\">",
						"</a>\n",
						"</td>\n";
					}

			echo	"</tr>\n";
		}

		echo	"</table>\n"; 
		if ($_GET['cat_id']<>'1')
		{
			echo "<p><b>",get_lang("AddForCat")," ",$ctg,"</b></p>",

				"<form action=\"forum_admin.php?forumgoadd=yes&ctg=$ctg&cat_id=$cat_id\" method=post>\n",

				"<input type=hidden name=cat_id value=\"$cat_id\">\n",
				"<input type=hidden name=forumgoadd value=yes>\n",

				"<table border=0>\n",
				"<tr  valign=\"top\">\n",
				"<td align=\"right\">",get_lang("ForName")," : </td>\n",
				"<td><input type=text name=forum_name size=40></td>\n",
				"</tr>\n",

				"<tr  valign=\"top\">\n",
				"<td align=\"right\">",get_lang("Description")," : </td>\n",
				"<td><textarea name=forum_desc cols=40 rows=3></textarea></td>\n",
				"</tr>\n",

				"<tr>\n",
				"<td>\n",
				"</td>\n",
				"<td>\n",
				"<input type=submit value=\"",get_lang("Add"),"\">\n",
				"</td>\n",
				"</table>\n",


				"</form>\n";
		}
}

/*==========================
      EDIT FORUM NAME
  ==========================*/

	elseif($forumgoedit)
	{
		$result = mysql_query("SELECT forum_id, forum_name, forum_desc, forum_access,
									  forum_moderator, cat_id, forum_type
								FROM `$TBL_FORUMS` WHERE forum_id='$forum_id'");

		list($forum_id, $forum_name, $forum_desc, $forum_access,
		     $forum_moderator, $cat_id_1, $forum_type)
		     = mysql_fetch_row($result);

		echo	"<h3>",
				$nameTools,"</h3>";
		
		echo
				"<b",get_lang("Modify")," ",$forum_name,"</b><br>",

				"<form action=\"forum_admin.php?forumgosave=yes&ctg=$ctg&cat_id=$cat_id\" method=post>\n",

				"<input type=hidden name=forum_id value=$forum_id>\n",

				"<table border=\"0\">\n",

				"<tr>\n",
				"<td align=\"right\">",get_lang("ForName")," : </td>\n",
				"<td><input type=text name=forum_name size=50 value=\"".htmlentities($forum_name)."\"></td>\n",
				"</tr>\n",

				"<tr valign=\"top\">\n",
				"<td align=\"right\">",get_lang("Description")," : </td>\n",
				"<td><textarea name=forum_desc cols=50 rows=3>",htmlentities($forum_desc),"</textarea></td>\n",
				"</tr>\n",

				"<tr>\n",
				"<td align=\"right\">",get_lang("ChangeCat")," : </td>\n",
				"<td><select name=cat_id>\n";

		$result = mysql_query("SELECT cat_id, cat_title FROM `$TBL_CATAGORIES`");

		while(list($cat_id, $cat_title) = mysql_fetch_row($result))
		{

			if ($cat_id == $cat_id_1)
			{
				echo "<option value=\"",$cat_id,"\" selected>",$cat_title,"</option>";
			}
			else
			{
				echo "<option value=\"",$cat_id,"\">",$cat_title,"</option>";
			}
		}

		echo	"</select>\n",

				"</td>\n",
				"</tr>\n",
				"<tr valign=\"top\">\n",
				"<td>\n",
				"</td>\n",
				"<td align=\"center\">\n",
				"<input type=submit value=\"", get_lang("Save"), "\">\n",
				"</td>",
				"</tr>\n",
				"</table>\n",
				"<input type=hidden name=forumgosave value=yes>\n",

				"</form>\n";
	}

/*==========================
    FORUM CATEGORY EDIT
  ==========================*/

	elseif($forumcatedit)
	{
		$result = mysql_query("select cat_id, cat_title from `$TBL_CATAGORIES` where cat_id='$cat_id'");
		list($cat_id, $cat_title) = mysql_fetch_row($result);

		echo	"<h3>",
				$nameTools,"</H3>";
		
		echo
				"<b>",get_lang("ModCatName"),"</b></br>",
				"<form action=\"forum_admin.php?forumcatsave=yes\" method=post>\n",
				"<input type=hidden name=cat_id value=$cat_id>\n",
				get_lang("Cat")," : ",
				"<input type=text name=cat_title size=55 value=\"",htmlentities($cat_title),"\">\n",
				"<input type=submit value=\"",get_lang("Save"),"\">\n",
				"</form>";

	//   <input type=hidden name=forumcatsave value=yes>
	}

/*==========================
     FORUM CATEGORY SAVE
  ==========================*/


	elseif ($forumcatsave)
	{
		mysql_query("update `$TBL_CATAGORIES` set cat_title='$cat_title' where cat_id='$cat_id'");

		// echo "<META http-equiv=\"REFRESH\" CONTENT=\"0; URL=\"".$_SERVER['PHP_SELF']."?forumadmin=yes\"> ";

		echo get_lang("NameCat") . ", &nbsp;<a href=\"".$_SERVER['PHP_SELF']."?forumadmin=yes\">" . get_lang("Back") . "</a>";
	}

/*=============================
  SAVE FORUM NAME & DESCRIPTION
  =============================*/

	elseif($forumgosave)
	{
		$result = mysql_query("select user_id from `$TBL_USERS` where username='$forum_moderator'");
		list($forum_moderator) = mysql_fetch_row($result);

		mysql_query("update `$TBL_USERS` set user_level='2' where user_id='$forum_moderator'");

		mysql_query("UPDATE `$TBL_FORUMS`
		             SET forum_name='$forum_name',
		                 forum_desc='$forum_desc',
		                 forum_access='2',
		                 forum_moderator='1',
		                 cat_id='$cat_id',
		                 forum_type='$forum_type'
		             WHERE forum_id='$forum_id'");

				echo	"<h3>",
				$nameTools,
				"</h3>",

				"<a href=\"".$_SERVER['PHP_SELF']."?forumgo=yes&cat_id=$cat_id&ctg=$ctg\">",get_lang("Back"),"</a>";

	}

/*==========================
     FORUM ADD CATEGORY
  ==========================*/

	elseif($add_forum_category)
	{
		mysql_query("INSERT INTO `$TBL_CATAGORIES` VALUES (NULL, '$categories', NULL)");

		echo	"<br><br><br><br><br>",get_lang("CatAdded"),
				",&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?forumadmin=yes\">",get_lang("Back"),"</a><br><br><br>";
	}

/*==========================
          Forum Go Add
  ==========================*/

	elseif($forumgoadd)
	{
		$result = mysql_query("SELECT user_id FROM `$TBL_USERS` WHERE username='$forum_moderator'");

		list($forum_moderator) = mysql_fetch_row($result);

		mysql_query("UPDATE `$TBL_USERS` SET user_level='2' WHERE user_id='$forum_moderator'");

		api_sql_query("INSERT INTO `$TBL_FORUMS`
		             (forum_id, forum_name, forum_desc, forum_access,
		              forum_moderator, cat_id, forum_type)
		             VALUES
		             (NULL, '$forum_name', '$forum_desc', '2',
		              '1', '$cat_id', '$forum_type')",__FILE__,__LINE__);

		$idforum=mysql_query("SELECT forum_id FROM `$TBL_FORUMS` WHERE forum_name='$forum_name'");

		while ($my_forum_id = mysql_fetch_array($idforum))
		{
			$forid = $my_forum_id[0];
		}

		mysql_query("INSERT INTO `$TBL_FORUM_MODS` (forum_id, user_id) VALUES ('$forid', '1')");

		echo	"<h3>",
				$nameTools,
				"</h3><br><br>",

				"<a href=\"".$_SERVER['PHP_SELF']."?forumgo=yes&cat_id=$cat_id&ctg=$ctg\">" . get_lang("Back") . "</a><br><br>\n";
	}

/*==========================
    FORUM DELETE CATEGORY
  ==========================*/

	elseif($forumcatdel)
	{
		if ($cat_id!=1)
		{
			$result = mysql_query("SELECT forum_id FROM `$TBL_FORUMS` where cat_id='$cat_id'");

			while(list($forum_id) = mysql_fetch_row($result))
			{
				mysql_query("DELETE FROM `$TBL_FORUMTOPICS` WHERE forum_id='$forum_id'");
			}

			mysql_query("DELETE FROM `$TBL_FORUMS` WHERE cat_id='$cat_id'");
			mysql_query("DELETE FROM `$TBL_CATAGORIES` WHERE cat_id='$cat_id'");

			echo	"<h3>",
					$nameTools,
					"</h3>",

					"<a href=\"".$_SERVER['PHP_SELF']."?forumadmin=yes\">" . get_lang("Back") . "</a>";
		}
		else
		{
			echo	"<h3>",
					$nameTools,
					"</h3>",
					get_lang("YouCannotDelCatOfGroupsForums"), "<br>",
					"<a href=\"".$_SERVER['PHP_SELF']."?forumadmin=yes\">" . get_lang("Back") . "</a>";
		}
	}

/*==========================
       FORUM GO DEL
  ==========================*/

	elseif($forumgodel)
	{
		mysql_query("DELETE FROM `$TBL_FORUMTOPICS` WHERE forum_id='$forum_id'");
		mysql_query("DELETE FROM `$TBL_FORUMS` WHERE forum_id='$forum_id'");

		// Added by Patrick Cool, 12 feb 2004
		// This is to reset the forum_id to null in the student_group table, which hides the link of the forum is none is present
		// group forums can be deleted through forum_admin.php and if the forum link is still visible in group_space.php this produces
		// a phpbb error.
		$TABLEGROUP		= $_course['dbNameGlu']."group_info";
		mysql_query("UPDATE `$TABLEGROUP` SET forum_id=NULL where forum_id=$forum_id");
		// End added by Patrick Cool, 12 feb 2004

		echo	"<h3>",
				$nameTools,
				"</h3>",

				"<a href=\"".$_SERVER['PHP_SELF']."?forumgo=yes&ctg=$ctg&cat_id=$cat_id\">",get_lang("Back"),"</a>";
	}

/*========================================================================*/

else
{
	echo	"<h3>",
			$nameTools,"</H3>";
	
	echo 
			"<b>",get_lang("ForCategories"),"</b><br>",
			"<p>",get_lang("AddForums"),

			"<form action=\"forum_admin.php?forumadmin=yes\" method=\"post\">",
			"</td>",

			"<tr>",
			"<td>",
			"<table BORDER=\"0\" CELLSPACING=\"2\" CELLPADDING=\"4\" width=\"100%\">",
			"<tr bgcolor=\"#E6E6E6\">",
			"<td>",get_lang("Categories"),"</td>",
			"<td align=\"center\">",get_lang("NbFor"),"</td>";
	echo "<td align=\"center\">",get_lang("Modify"),"</td>";
	if ($is_allowed[DELETE_RIGHT]) echo "<td align=\"center\">",get_lang("Delete"),"</td>";
	echo "</tr>";

	$result = mysql_query("select cat_id, cat_title from `$TBL_CATAGORIES` ORDER BY cat_id");

	$i=0;

	while(list($cat_id, $cat_title) = mysql_fetch_row($result))
	{
		$gets = mysql_query("select count(*) as total from `$TBL_FORUMS` where cat_id='$cat_id'");
		$numbers= mysql_fetch_array($gets);

		if($i%2==0)
		{
			echo "<tr>";
		}
		elseif($i%2==1)
		{
			echo "<tr>";
		}

		echo	"<td><small>",$cat_id,".</small> ",$cat_title,"</td>\n",
				"<td align=\"center\">",
				$numbers[total]," ",
				"<a href=\"forum_admin.php?forumgo=yes&cat_id=$cat_id&ctg=",urlencode($cat_title),"\">",
				get_lang("Forums"),
				"</a>",
				"</td>\n",
				"<td align=\"center\">",
				"<a href=\"forum_admin.php?forumcatedit=yes&cat_id=$cat_id\">",
				"<img src=\"../img/edit.gif\" alt=\"",get_lang("Modify"),"\" border=\"0\">",
				"</a>",
				"</td>\n",
				"<td align=\"center\">";
		if ($cat_id!=1 && $is_allowed[DELETE_RIGHT])
			echo
				"<a href=\"forum_admin.php?forumcatdel=yes&cat_id=$cat_id&ok=0\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("ConfirmYourChoice")))."')) return false;\">",
				"<img src=\"../img/delete.gif\" alt=\"",get_lang("Delete"),"\" border=\"0\">",
				"</a>";
		echo
				"</td>\n",
				"</tr>\n";
		$i++;
	}

	echo	"</table>\n",

			"</form>\n",

			"<h4>",get_lang("AddCategory"),"</h4>\n",

			"<form action=\"forum_admin.php?add_forum_category=yes\" method=\"post\">\n",

			get_lang("Cat")," : ",
			"<input type=\"text\" name=\"categories\" size=\"50\">\n",
			"<input type=\"submit\" value=\"",get_lang("Add"),"\">\n",
			"<input type=\"hidden\" name=\"add_forum_category\" value=\"yes\">\n",

			"</form>\n";
	}
	/*========================================================================*/
}
else
{
	echo "You are not allowed here<br>";
}

/*
==============================================================================
		FOOTER 
==============================================================================
*/
Display::display_footer();
?>