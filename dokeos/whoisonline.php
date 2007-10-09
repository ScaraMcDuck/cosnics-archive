<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) various contributors

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
* Who is online list
*
* @todo clean code, do not use variables $t or $p !!!
==============================================================================
*/

include_once ("./main/inc/claro_init_global.inc.php");
api_use_lang_files('index','registration');
require_once('application/common/userdetails.class.php');
$track_user_table = Database :: get_main_table(MAIN_USER_TABLE);

/*
-----------------------------------------------------------
	Header
	include the HTTP, HTML headers plus the top banner
-----------------------------------------------------------
*/

Display :: display_header(get_lang('UsersOnLineList'));

// Who is logged in?
$MINUTE = 30;

// This if statement prevents users accessing the who's online feature when it has been disabled.
if ((get_setting('showonline', 'world') == "true" AND !api_get_user_id()) OR (get_setting('showonline', 'users') == "true" AND api_get_user_id()))
{
	$userlist = WhoIsOnline($_uid, $statsDbName, $MINUTE);

	echo "<b>".get_lang('TotalOnLine')." : ".count($userlist)."</b><br /><br />";
	$udm = UsersDataManager::get_instance();
	if ($userlist != false)
	{
		//	if (!IsValidUser($_GET["id"],$mysqlMainDb))
		if ($_GET["id"] == '')
		{
			echo "<a href=\"javascript:window.location.reload()\">".get_lang('Refresh')."</a>";
			foreach ($userlist as $row)
			{

				$uid = $row[0];
				$user = $udm->retrieve_user($uid);
				$table_row = array ();
				$url = '?id='.$user->get_user_id();
				if ($user->has_picture())
				{
					$table_row[] = '<span style="display:none;">1</span><a href="'.$url.'"><img src="'.$user->get_full_picture_url().'" alt="'.htmlentities($user->get_fullname()).'" width="40" border="0"/></a>';
				}
				else
				{
					$table_row[] = '<span style="display:none;">0</span>';
				}

				$table_row[] = '<a href="'.$url.'">'.$user->get_firstname().'</a>';
				$table_row[] = '<a href="'.$url.'">'.$user->get_lastname().'</a>';
				$table_row[] = Display :: encrypted_mailto_link($user->get_email());
				$table_row[] = $user->get_status() == 1 ? get_lang('Teacher') : get_lang('Student');
				$table_data[] = $table_row;
			}
			$table_header[] = array (get_lang('Picture'), true, 'width="50"');
			$table_header[] = array (get_lang('FirstName'), true);
			$table_header[] = array (get_lang('Lastname'), true);
			$table_header[] = array (get_lang('Email'), true);
			$table_header[] = array ('functie', true);
			$sorting_options['column'] = (isset ($_GET['column']) ? $_GET['column'] : 2);
			Display :: display_sortable_table($table_header, $table_data, $sorting_options, array ('per_page_default' => 1000));
		}
		else //individual list
			{
			$user = $udm->retrieve_user($_GET['id']);
			$userdetails = new UserDetails($user);
			echo $userdetails->toHtml();
		}
	}

	$referer = empty ($_GET['referer']) ? 'index.php' : $_GET['referer'];

	echo "<a href=\"". ($_GET['id'] ? "javascript:window.history.back();" : $referer)."\">< ".get_lang('Back')."</a>";
}

/*
==============================================================================
		FOOTER
==============================================================================
*/

Display :: display_footer();
?>

