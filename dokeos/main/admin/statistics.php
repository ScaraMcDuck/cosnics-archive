<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	@package dokeos.admin
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/

api_use_lang_files('admin');
$cidReset=true;
include('../inc/claro_init_global.inc.php');
$this_section=SECTION_PLATFORM_ADMIN;

api_protect_admin_script();

include_once(api_get_library_path().'/fileManage.lib.php');

$interbredcrump[]=array("url" => "index.php","name" => get_lang('PlatformAdmin'));
$tool_name = get_lang('Statistics');
Display::display_header($tool_name);

api_display_tool_title($tool_name);

/*
==============================================================================
		MAIN CODE
==============================================================================
*/ 

/*
==============================================================================
		FOOTER 
==============================================================================
*/ 
Display::display_footer();
?>