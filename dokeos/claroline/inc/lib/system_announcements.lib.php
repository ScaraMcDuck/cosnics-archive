<?php    // $Id$  
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) 2004 Bart Mollet (HoGent)
	
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
define('VISIBLE_GUEST', 1);
define('VISIBLE_STUDENT', 2);
define('VISIBLE_TEACHER', 3);
/**
============================================================================== 
*	This is the system announcements library for Dokeos.
*
*	@package dokeos.library
============================================================================== 
*/
class SystemAnnouncementManager
{
	/** 
	 * Displays all announcements
	 * @param int $visible VISIBLE_GUEST, VISIBLE_STUDENT or VISIBLE_TEACHER
	 * @param int $id The identifier of the announcement to display
	 */
	function display_announcements($visible, $id = -1)
	{
		$db_table = Database :: get_main_table(MAIN_SYSTEM_ANNOUNCEMENTS_TABLE);
		$sql = "SELECT *, DATE_FORMAT(date_start,'%d/%m/%y (%H:%i)') AS display_date FROM ".$db_table." WHERE NOW() BETWEEN date_start AND date_end  ";
		switch ($visible)
		{
			case VISIBLE_GUEST :
				$sql .= " AND visible_guest = '1'";
				break;
			case VISIBLE_STUDENT :
				$sql .= " AND visible_student = '1'";
				break;
			case VISIBLE_TEACHER :
				$sql .= " AND visible_teacher = '1'";
				break;
		}
		$sql .= " ORDER BY date_start DESC";
		$announcements = api_sql_query($sql,__FILE__,__LINE__);
		if (mysql_num_rows($announcements))
		{
			$query_string = ereg_replace('announcement=[1-9]+', '', $_SERVER['QUERY_STRING']);
			$query_string = ereg_replace('&$', '', $query_string);
			$url = $_SERVER['PHP_SELF'];
			echo '<div class="system_announcements">';
			echo '<h3>'.get_lang('SystemAnnouncements').'</h3>';
			echo '<ul>';
			while ($announcement = mysql_fetch_object($announcements))
			{
				echo '<li>';
				if ($id != $announcement->id)
				{
					if (strlen($query_string) > 0)
					{
						$show_url = $url.'?'.$query_string.'&announcement='.$announcement->id;
					}
					else
					{
						$show_url = $url.'?announcement='.$announcement->id;
					}
					echo '<div class="system_announcement"><div class="system_announcement_title"><a href="'.$show_url.'">'.$announcement->title.'</a></div></div>';
				}
				else
				{
					echo '<div class="system_announcement"><div class="system_announcement_title"><a href="'.$url.'?'.$query_string.'">'.$announcement->title.'</a></div><div class="system_announcement_content">'.$announcement->content.'</div>';
				}
				echo '</li>';
			}
			echo '</ul>';
			echo '</div>';
		}
		return;
	}
	/**
	 * Get all announcements
	 * @return array An array with all available system announcements (as php
	 * objects)
	 */
	function get_all_announcements()
	{
		$db_table = Database :: get_main_table(MAIN_SYSTEM_ANNOUNCEMENTS_TABLE);
		$sql = "SELECT *, IF( NOW() BETWEEN date_start AND date_end, '1', '0') AS visible FROM ".$db_table." ORDER BY date_start ASC";
		$announcements = api_sql_query($sql,__FILE__,__LINE__);
		$all_announcements = array();
		while ($announcement = mysql_fetch_object($announcements))
		{
			$all_announcements[] = $announcement;
		}
		return $all_announcements;
	}
	/**
	 * Adds an announcement to the database
	 * @param string $title Title of the announcement
	 * @param string $content Content of the announcement
	 * @param string $date_start Start date (YYYY-MM-DD HH:II: SS)
	 * @param string $date_end End date (YYYY-MM-DD HH:II: SS)
	 */
	function add_announcement($title, $content, $date_start, $date_end, $visible_teacher = false, $visible_student = false, $visible_guest = false)
	{
		$db_table = Database :: get_main_table(MAIN_SYSTEM_ANNOUNCEMENTS_TABLE);
		$sql = "INSERT INTO ".$db_table." (`title`,`content`,`date_start`,`date_end`,`visible_teacher`,`visible_student`,`visible_guest`) 
												VALUES ('".$title."','".$content."','".$date_start."','".$date_end."','".$visible_teacher."','".$visible_student."','".$visible_guest."')";
		return api_sql_query($sql,__FILE__,__LINE__);
	}
	/**
	 * Updates an announcement to the database
	 * @param integer $id Id of the announcement
	 * @param string  $title Title of the announcement
	 * @param string  $content Content of the announcement
	 * @param string $date_start Start date (YYYY-MM-DD HH:II: SS)
	 * @param string $date_end End date (YYYY-MM-DD HH:II: SS)	 
	 */ 
	function update_announcement($id, $title, $content, $date_start, $date_end, $visible_teacher = false, $visible_student = false, $visible_guest = false)
	{
		$db_table = Database :: get_main_table(MAIN_SYSTEM_ANNOUNCEMENTS_TABLE);
		$sql = "UPDATE ".$db_table." SET title='".$title."',content='".$content."',date_start='".$date_start."',date_end='".$date_end."', ";
		$sql .= " visible_teacher = '".$visible_teacher."', visible_student = '".$visible_student."', visible_guest = '".$visible_guest."' WHERE id='".$id."'";
		return api_sql_query($sql,__FILE__,__LINE__);
	}
	/**
	 * Deletes an announcement
	 * @param integer $id The identifier of the announcement that should be
	 * deleted
	 */
	function delete_announcement($id)
	{
		$db_table = Database :: get_main_table(MAIN_SYSTEM_ANNOUNCEMENTS_TABLE);
		$sql = "DELETE FROM ".$db_table." WHERE id='".$id."'";
		return api_sql_query($sql,__FILE__,__LINE__);
	}
	/**
	 * Gets an announcement
	 * @param integer $id The identifier of the announcement that should be
	 * deleted
	 */
	function get_announcement($id)
	{
		$db_table = Database :: get_main_table(MAIN_SYSTEM_ANNOUNCEMENTS_TABLE);
		$sql = "SELECT * FROM ".$db_table." WHERE id='".$id."'";
		$announcement = mysql_fetch_object(api_sql_query($sql,__FILE__,__LINE__));
		return $announcement;
	}
	/**
	 * Change the visibility of an announcement
	 * @param integer $announcement_id
	 * @param integer $user For who should the visibility be changed (possible
	 * values are VISIBLE_TEACHER, VISIBLE_STUDENT, VISIBLE_GUEST)
	 */
	function set_visibility($announcement_id, $user, $visible)
	{
		$db_table = Database :: get_main_table(MAIN_SYSTEM_ANNOUNCEMENTS_TABLE);
		$field = ($user == VISIBLE_TEACHER ? 'visible_teacher' : ($user == VISIBLE_STUDENT ? 'visible_student' : 'visible_guest'));
		$sql = "UPDATE ".$db_table." SET ".$field." = '".$visible."' WHERE id='".$announcement_id."'";
		return api_sql_query($sql,__FILE__,__LINE__);
	}
}
?>