<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University
	Copyright (c) 2001 Universite Catholique de Louvain
	Copyright (c) various contributors
	Copyright (c) Bart Mollet, Hogeschool Gent

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
*                  HOME PAGE FOR EACH COURSE (BASIC TOOLS FIXED)
*
*	This page, included in every course's index.php is the home
*	page.To make administration simple, the professor edits his
*	course from it's home page. Only the login detects that the
*	visitor is allowed to activate, deactivate home page links,
*	access to Professor's tools (statistics, edit forums...).
*
*	@package dokeos.course_home
============================================================================== 
*/
include('../../main/course_home/btf_functions.php');  // RH: extra ../

/*==========================
     TOOLS  FOR  EVERYBODY
  ==========================*/

showtools2('Basic',$role_id,$is_allowed);
showtools2('External',$role_id,$is_allowed);

/*==========================
       PROF ONLY VIEW
  ==========================*/

if ($is_allowed[EDIT_RIGHT])
{
	echo '<hr noshade="noshade"/>';
	echo '<strong>'.get_lang('CourseAdminOnly').'</strong>';
	showtools2('courseAdmin',$role_id,$is_allowed);
}

/*--------------------------------------
       TOOLS FOR PLATFORM ADMIN ONLY
  --------------------------------------*/

if ($is_platformAdmin and $is_allowed[EDIT_RIGHT])
{
	echo '<hr noshade="noshade"/>';
	echo '<strong>'.get_lang('PlatformAdminOnly').'</strong>';
	showtools2('claroAdmin',$role_id,$is_allowed);
}
?>