<?php  
/**
============================================================================== 
*	@package dokeos.forum
============================================================================== 
*/

/***************************************************************************
                            threadedview.inc.php  -  description
                             -------------------
    begin                : Friday July 1 2003
    copyright            : (C) 2003 Patrick Cool
    email                : patrick.cool@UGent.be
  
***************************************************************************/

/***************************************************************************
This is a feature add-on for phpBB used in Claroline 1.4. A lot of the features 
of the original phpBB have been stripped out of Claroline 1.4. 
I added my own code so you could choose if you wanted to see a threaded view
or a flat view
***************************************************************************/

/***************************************************************************
 *                                         				                                
 *   This program is free software; you can redistribute it and/or modify  	
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or	    	
 *   (at your option) any later version.
 *
 ***************************************************************************/

function display_kids ($topic_id)
{
global $tbl_posts;
global $tbl_posts_text;
global $db;
global $level;
global $topic;
global $forum;
global $postid;
	$sql2 = "SELECT p.*, pt.post_title 
       FROM `$tbl_posts` p, `$tbl_posts_text` pt
       WHERE parent_id = '".$topic_id."'
       AND p.post_id = pt.post_id
       ORDER BY post_id";
	$result2 = mysql_query($sql2, $db) OR error_die("<big>An Error Occured</big><hr>Could not connect to the Posts database. $sql2");
	// number reports how much replies there are for that specific level
	$number=mysql_num_rows($result2);
	$myrow2 = mysql_fetch_array($result2);
	
	$level=$level+1;
	$volgnummer=1;
	do
	{
	// we are looking for the number of replies posted on this message
	$sql3 = "SELECT p.*, pt.post_text 
       FROM `$tbl_posts` p, `$tbl_posts_text` pt
       WHERE parent_id = '".$myrow2[post_id]."'
       AND p.post_id = pt.post_id
       ORDER BY post_id";
	$result3 = mysql_query($sql3, $db) OR error_die("<big>An Error Occured</big><hr>Could not connect to the Posts database. $sql3");
	$numberreplies=mysql_num_rows($result3);

	
	
	if ($number<>0)
		{ 
			echo "<tr>\n";
			echo "<td>";
			echo "<table cellSpacing=0 cellPadding=0 border=0><tr><td>";
			// the number of | images depends on the level of the message
			// the | image (i.gif) has to be shown $level-1 times
			if ($level>1)
				{
				$count=0;
					do
					{
					echo "<img src='../img/i.gif'>";
					$count=$count +1;
					} while ($count<$level-1);

				}
			if ($numberreplies<>0 )
				{
				echo "<img src='../img/t.gif'><img src='../img/m.gif'>";
				}
			elseif ($numberreplies==0 and $volgnummer<>$number)
				{
				echo "<img src='../img/t.gif'><img src='../img/c.gif'>";
				}
	
			elseif ($numberreplies==0 and $volgnummer==$number)
				{
				echo "<img src='../img/l.gif'><img src='../img/c.gif'>";
				}
			echo "</td><td>";
			if ($postid<>$myrow2[post_id])
				echo "&nbsp;<a href='".$_SERVER['PHP_SELF']."?topic=".$topic."&forum=".$forum."&postid=".$myrow2[post_id]."&forumview=threaded'>".$myrow2[post_title]."</a> <font size=2>(".date("d/m/y G:i",convert_mysql_date($myrow2[post_time])).")</font>";
			else
				echo "&nbsp;<b>".$myrow2[post_title]."</b> <font size=2>(".date("d/m/y G:i",convert_mysql_date($myrow2[post_time])).")</font>";
			echo "</td></tr></table>";
			echo "</td>\n";
			echo "</tr>\n";
			
		display_kids($myrow2[post_id]);		
		}
	else 
		{
		
		}
	$volgnummer=$volgnummer+1;
	}while($myrow2 = mysql_fetch_array($result2));
$level=$level -1;
}			
				
?>