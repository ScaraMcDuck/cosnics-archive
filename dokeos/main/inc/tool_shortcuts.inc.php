<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Patrick Cool, Ghent University
	
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
* Description:
* This puts shortcut icons on each page of the course and allows
* instant switching to a tool without going back to the course homepage
* @package dokeos.include
* @deprecated This file is no longer in use
==============================================================================
*/

if (isset($_cid))
{
	// displaying the home link that goes to the course homepage
	echo "<a href=\"".api_get_path(WEB_COURSE_PATH).$_course['path']."/index.php\" target=\"_top\"><img src=\"".api_get_path(WEB_CODE_PATH)."img/home.gif\" title=\"".get_lang('CourseHomepage')."\"  alt=\"".get_lang('CourseHomepage')."\" /></a>";
	echo "&nbsp;";
	// Displaying the course tools, only the visible tools that are not added (default tools)
	$tool_table = Database::get_course_table(TOOL_LIST_TABLE);
	$sql="SELECT * FROM $tool_table WHERE visibility='1' and admin='0' and added_tool!='1' ORDER BY id ASC";
	$result=api_sql_query($sql,__FILE__,__LINE__);
	
	while ($row=mysql_fetch_array($result))
	{
		$link=$row['link'];
		$link=$rootWeb.substr_replace($link,"", 0,3);
	
		// NOTICE : table tool no longer contains "../claroline/img/" but only the image file name
	
		if(!stristr($row['link'],'http://'))
		{
			$row['link']=api_get_path(WEB_CODE_PATH).$row['link'];
		}
		
		$row['name'] = $row['image']== "scormbuilder.gif" ? $row['name'] : get_lang($row['name']);
		
		if(strstr($row['link'],'?'))
		{
			//link already contains a parameter, add course id parameter with &
			$parameter_separator = "&";
		}
		else
		{
			//link doesn't contain a parameter yet, add course id parameter with ?
			$parameter_separator = "?";
		}
		echo "<a href=\"".$row['link'].$parameter_separator.api_get_cidreq()."\" target=\"_top\"><img src=\"".api_get_path(WEB_CODE_PATH)."img/".$row['image']."\" title=\"".$row['name']."\" alt=\"".$row['name']."\" /></a>";
	}
} // end if (isset($_cid))
?>
