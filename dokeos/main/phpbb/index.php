<?php

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

/**
==============================================================================
*	@package dokeos.forum
==============================================================================
*/

/***************************************************************************
                          index.php  -  description
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
require("auth.php");
$pagetitle = $l_indextitle;
$pagetype = "index";
include('page_header.php');

//stats
include(api_get_library_path()."/events.lib.inc.php");
require_once(api_get_library_path()."/groupmanager.lib.php");

event_access_tool(TOOL_BB_FORUM);

$result = api_sql_query("SELECT `c`.* FROM `$tbl_catagories` c, `$tbl_forums` f
                       WHERE `f`.`cat_id` = `c`.`cat_id`
                       GROUP BY `c`.`cat_id`, `c`.`cat_title`, `c`.`cat_order`
                       ORDER BY `c`.`cat_id` DESC",__FILE__,__LINE__);

$total_categories = mysql_num_rows($result);

$sqlGroupsOfCurrentUser ="
SELECT `g`.`forum_id`
	FROM `".$tbl_student_group."` `g`,
		 `".$tbl_user_group."` `gu`
	WHERE
		`g`.`id` = `gu`.`group_id`
		AND
		`gu`.`user_id` = '".$_uid."'";

$resGroupsOfCurrentUser = api_sql_query($sqlGroupsOfCurrentUser,__FILE__,__LINE__);

//$DEBUG = true;
//printVar($sqlGroupsOfCurrentUser,"GroupsOfCurrentUser");
$arrGroupsOfCurrentUser = array();
while ( $thisGroups = mysql_fetch_array($resGroupsOfCurrentUser,MYSQL_ASSOC))
{
	$arrGroupsOfCurrentUser[] = $thisGroups["forum_id"];
};

//Introduction section
Display::display_introduction_section(TOOL_BB_FORUM, $is_allowed);
?>

<table width="100%" border="0" cellpadding="1" cellspacing="1">

<?php

if($total_categories)
{
	if(!$viewcat)
	{
		$viewcat = -1;
	}

	while($cat_row = mysql_fetch_array($result))
	{
		$categories[] = $cat_row;
	}

	$limit_forums = "";

	if($viewcat != -1)
	{
		$limit_forums = " f.cat_id = '$viewcat'";
	}

	$sql_f = "SELECT f.*, u.username, u.user_id, p.post_time, g.id gid
	                      FROM `$tbl_forums` f
	                      LEFT JOIN `$tbl_posts` p ON p.post_id = f.forum_last_post_id
	                      LEFT JOIN `$tbl_users` u ON u.user_id = p.poster_id
	                      LEFT JOIN `".$tbl_student_group."` g ON g.forum_id = f.forum_id
	                      $limit_forums
	                      ORDER BY f.cat_id, f.forum_id";
	$f_res = api_sql_query($sql_f,__FILE__,__LINE__);

	while($forum_data = mysql_fetch_array($f_res))
	{
		if(!is_null($forum_data['gid']))
		{
			if(GroupManager::user_has_access($_uid,$forum_data['gid'],GROUP_TOOL_FORUM))
			{
				$forum_row[] = $forum_data;	
			}	
		}
		else
		{
			$forum_row[] = $forum_data;	
		}
		
		
	}

	for($i = 0; $i < $total_categories; $i++)
	{
		if($viewcat != -1)
		{
			if($categories[$i][cat_id] != $viewcat)
			{
				$title = stripslashes($categories[$i][cat_title]);

				echo	"<tr class=\"cell_header\" align=\"left\" valign=\"top\">\n\n",
						"<td colspan=6>\n",
						"<b>",$title,"</b>\n",
						"</td>\n",
						"</tr>\n\n";
?>
<tr bgcolor="$color2" align="center">

<td colspan="2" align="left"><?php echo     $l_forum   ?></td>
<td><?php echo  $l_topics  ?></td>
<td><?php echo  $l_posts   ?></td>
<td><?php echo  $l_lastpost?></td>

</tr>
<?php
				continue;
			}
		}

		$title = stripslashes($categories[$i]['cat_title']);

		/*
		 * Added by Thomas for Claroline :
		 * distinguish group forums from others
		 */
		$catNum = $categories[$i][cat_id];

		/* category title */

		echo	"<tr class=\"cell_header\" align=\"left\" valign=\"top\">\n\n",
				"<td colspan=6>\n",
				"<b>",$title,"</b>\n",
				"</td>\n",
				"</tr>\n\n";
?>
<tr bgcolor="<?php echo $color1?>" align="center">

<td colspan="2" align="left"><?php echo $l_forum?></td>
<td><?php echo $l_topics?></td>
<td><?php echo $l_posts?></td>
<td><?php echo $l_lastpost?></td>

</tr>
<?php

		@reset($forum_row);

		for($x = 0; $x < count($forum_row); $x++)
		{
			unset($last_post);

			if($forum_row[$x]["cat_id"] == $categories[$i]["cat_id"])
			{
				if($forum_row[$x]["post_time"])
				{
					$last_post = $forum_row[$x]["post_time"]; // post time format  datetime de mysql
				}

				$last_post_datetime                    = $forum_row[$x]["post_time"];
				list($last_post_date, $last_post_time) = split(" ", $last_post_datetime);
				list($year, $month, $day)              = explode("-", $last_post_date);
				list($hour, $min)                      = explode(":", $last_post_time);
				$last_post_time                        = mktime($hour, $min, 0, $month, $day, $year);

				// $last_post_time  mktime du champs  post_time.
				if(empty($last_post))
				{
					$last_post = "No Posts";
				}

				echo "<tr  align=\"left\" valign=\"top\">\n\n";

				if($last_post_time > $last_visit && $last_post != "No Posts")
				{
					echo	"<td align=\"center\" valign=\"top\" width=5%>\n",
							"<img src=\"../img/red_folder.gif\">\n";
							"</td>\n";
				}
				else
				{
					echo	"<td align=\"center\" valign=\"top\" width=5%>\n",
							"<img src=\"../img/folder.gif\">\n",
							"</td>\n";
				}

				$name = stripslashes($forum_row[$x][forum_name]);
				$total_posts = $forum_row[$x]["forum_posts"];
				$total_topics = $forum_row[$x]["forum_topics"];
				$desc = $forum_row[$x][forum_desc];
				$desc = api_parse_tex($desc);
				$desc = stripslashes($desc);
				
				echo	"<td>\n";

				$forum=$forum_row[$x]["forum_id"];

				/*
				 * Claroline feature added by Thomas July 2002
				 * Visit only my group forum if not admin or tutor
				 * If tutor, see all groups but indicate my groups
				 */


				/*--------------------------------------
				              TUTOR VIEW
				  --------------------------------------*/

				if($tutorCheck==1)
				{
					$sqlTutor=api_sql_query("SELECT id FROM `$tbl_student_group` WHERE forum_id='$forum' AND tutor_id='$_uid'",__FILE__,__LINE__);

					$countTutor = mysql_num_rows($sqlTutor);
					// echo "<br>forum $forum count tutor $countTutor<br>";

					if ($countTutor==0)
					{
						echo	"<a href=\"viewforum.php?".api_get_cidreq()."&gidReq=",$forum_row[$x]["gid"],"&forum=",$forum_row[$x]["forum_id"],"&",$total_posts,"\">",
								$name,
								"</a>\n";
					}
					else
					{
						echo	"<a href=\"viewforum.php?gidReq=",$forum_row[$x]["gid"],"&forum=",$forum_row[$x]["forum_id"],"&",$total_posts,"\">",
								$name,
								"</a>\n",
								"&nbsp;(",$langOneMyGroups,")";
					}
				}


				/*--------------------------------------
				               ADMIN VIEW
				  --------------------------------------*/

				elseif($is_courseAdmin)
				{
					echo	"<a href=\"viewforum.php?gidReq=",$forum_row[$x]["gid"],"&forum=",$forum_row[$x]['forum_id'],"&",$total_posts,"\">",
							$name,
							"</a>\n";
				}



				/*--------------------------------------
				              STUDENT VIEW
				  --------------------------------------*/

				elseif($catNum == 1)
				{
					if (in_array($forum, $arrGroupsOfCurrentUser)) // this  cond  must change.
					{
						echo	"<a href=\"viewforum.php?".api_get_cidreq()."&gidReq=",$forum_row[$x]["gid"],"&forum=",$forum_row[$x]["forum_id"],"&$total_posts\">",
								$name,
								"</a>\n",
								"&nbsp;&nbsp;(",$langMyGroup,")\n";
					}
					else
					{
						echo	"<a href=\"viewforum.php?".api_get_cidreq()."&gidReq=",$forum_row[$x]["gid"],"&forum=",$forum_row[$x]["forum_id"],"&$total_posts\">",
									$name,
									"</a>\n";
					}
				}

				/* OTHER FORUMS */
				else
				{
					echo	"<a href=\"viewforum.php?".api_get_cidreq()."&gidReq=",$forum_row[$x]["gid"],"&forum=",$forum_row[$x]["forum_id"],"&$total_posts\">",
							$name,
							"</a> ";
				}



				echo	"<br>",$desc,"\n",
						"</td>\n",

						"<td width=5% align=\"center\" valign=\"middle\">\n",
						$total_topics,"\n",
						"</td>\n",

						"<td width=5% align=\"center\" valign=\"middle\">\n",
						$total_posts,"\n",
						"</td>\n",

						"<td width=15% align=\"center\" valign=\"middle\">\n",
						$last_post,
						"</td>\n";

				$forum_moderators = get_moderators($forum_row[$x][forum_id], $db);

				echo	"</tr>\n";
			}
		}
	}
}

?>
</table>
<?php
require('page_tail.php'); // include the claro footer.
?>
