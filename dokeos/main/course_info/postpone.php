<?php  // $Id$
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
* MODIFY COURSE INFO                                          |
* Modify course settings like:										 |
* 1. Course title													 |
* 2. Department													 |
* 3. Course description URL in the university web					 |
* Course code cannot be modified, because it gives the name for the	 |
* course database and course web directory. Professor cannot be 		 |
* changed either as it determines who is allowed to modify the course. |
*
*
*	@author Thomas Depraetere
*	@author Hugues Peeters
*	@author Christophe Gesche
*
*	@package dokeos.course_info
==============================================================================
*/
	
/*
==============================================================================
		INIT SECTION
==============================================================================
*/ 

$langFile = "postpone";
//$interbredcrump[]= array ("url"=>"index.php", "name"=> get_lang('Admin'));
$htmlHeadXtra[] = "
<style type=\"text/css\">
/*<![CDATA[*/
.month {font-weight : bold;color : #FFFFFF;background-color : #4171B5;padding-left : 15px;padding-right : 15px;}
.content {position: relative; left: 25px;}
/*]]>*/
</style>
<style media=\"print\" type=\"text/css\">
/*<![CDATA[*/
td {border-bottom: thin dashed gray;}
/*]]>*/
</style>";
include('../inc/claro_init_global.inc.php'); 
$this_section=SECTION_COURSES;

include(api_get_library_path()."/text.lib.php"); 
include(api_get_library_path()."/debug.lib.inc.php"); 
Display::display_header($nameTools,"Settings");

//include(api_get_configuration_path()."/postpone.conf.php");

$nameTools = get_lang('Postpone');
$TABLECOURSE	= Database::get_main_table(MAIN_COURSE_TABLE); 
$is_allowedToEdit 			= $is_courseAdmin;
$currentCourseID 			= $_course['sysCode'];
$currentCourseRepository 	= $_course["path"];


$sqlCourseExtention 			= "SELECT last_visit, last_edit, creation_date, expiration_date FROM ".$TABLECOURSE." WHERE code = '".$_cid."'";
$resultCourseExtention 			= api_sql_query($sqlCourseExtention,__FILE__,__LINE__);
$currentCourseExtentionData 	= mysql_fetch_array($resultCourseExtention);
$currentCourseLastVisit 		= $currentCourseExtentionData["last_visit"];
$currentCourseLastEdit			= $currentCourseExtentionData["last_edit"];
$currentCourseCreationDate 		= $currentCourseExtentionData["creation_date"];
$currentCourseExpirationDate	= $currentCourseExtentionData["expiration_date"];
// HERE YOU CAN EDIT YOUR RULES TO EXTEND THE LIFE OF COURSE

// $newCourseExpirationDate	= now() + $extendDelay



?>
<h3>
	<?php echo $nameTools ?>
</h3>
<?php echo get_lang('SubTitle'); ?>


this script  would be  called  by  
	professor, 
	or administrator, 
	or other  script 
to give more time to a course before expiration

<?php
Display::display_footer();
?>
