<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert
	Copyright (c) Bart Mollet, Hogeschool Gent

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

api_use_lang_files('admin');
$cidReset = true;
include ('../inc/claro_init_global.inc.php');
$this_section=SECTION_PLATFORM_ADMIN;
require_once (api_get_library_path().'/classmanager.lib.php');
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');
api_protect_admin_script();
$form = new FormValidator('add_class');
$form->add_textfield('name',get_lang('ClassName'));
$form->addElement('submit','submit',get_lang('Ok'));
if($form->validate())
{
	$values = $form->exportValues();
	ClassManager :: create_class($values['name']);
	header('Location: class_list.php');
}
$tool_name = get_lang("AddClasses");
$interbredcrump[] = array ("url" => "index.php", "name" => get_lang('PlatformAdmin'));
Display :: display_header($tool_name);
api_display_tool_title($tool_name);
$form->display();
/*
==============================================================================
		FOOTER 
==============================================================================
*/
Display :: display_footer();
?>