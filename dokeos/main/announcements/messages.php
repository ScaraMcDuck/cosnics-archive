<?php // $Id$
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) (CESGA)
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

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
*	MESSAGES MODULE
*	This modules allows to send messages to selected users & groups from a course
*
*	@todo clean up code
*	@package dokeos.announcements
*
*	@author	Thomas Depraetere			(depraetere@ipm.ucl.ac.be)
*	@author	Hugues Peeters				(peeters@ipm.ucl.ac.be)
*	@author	Pablo Rey & Miguel Rubio 	(http://aula.cesga.es)
*	@author	Roan Embrechts				(roan.embrechts@vub.ac.be)
*	@author	Miguel Rubio 				(teleensino@cesga.es)
*	@author	Toon Van Hoecke				(toon.vanhoecke@ugent.be)
============================================================================== 
*/


/*
============================================================================== 
		INIT SECTION
============================================================================== 
*/ 

$langFile =	'announcements'; 
$courseadmins_already_selected = true;	// true : all course admins in the right select box by default
										// false : no entries in the right select box

/*
-----------------------------------------------------------
	Included libraries
-----------------------------------------------------------
*/ 
include('../inc/claro_init_global.inc.php'); //	settings initialisation	

$this_section=SECTION_COURSES;

include(api_get_library_path().'/text.lib.php');

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/ 
$htmlHeadXtra[]="<script type=\"text/javascript\">
/* <![CDATA[ Begin javascript menu swapper */

function move(fbox,	tbox)
{
	var	arrFbox	= new Array();
	var	arrTbox	= new Array();
	var	arrLookup =	new	Array();

	var	i;
	for	(i = 0;	i <	tbox.options.length; i++)
	{
		arrLookup[tbox.options[i].text]	= tbox.options[i].value;
		arrTbox[i] = tbox.options[i].text;
	}

	var	fLength	= 0;
	var	tLength	= arrTbox.length;

	for(i =	0; i < fbox.options.length;	i++)
	{
		arrLookup[fbox.options[i].text]	= fbox.options[i].value;

		if (fbox.options[i].selected &&	fbox.options[i].value != \"\")
		{
			arrTbox[tLength] = fbox.options[i].text;
			tLength++;
		} 
		else
		{
			arrFbox[fLength] = fbox.options[i].text;
			fLength++;
		}
	}

	arrFbox.sort();
	arrTbox.sort();
	fbox.length	= 0;
	tbox.length	= 0;

	var	c;
	for(c =	0; c < arrFbox.length; c++)
	{
		var	no = new Option();
		no.value = arrLookup[arrFbox[c]];
		no.text	= arrFbox[c];
		fbox[c]	= no;
	}
	for(c =	0; c < arrTbox.length; c++)
	{
		var	no = new Option();
		no.value = arrLookup[arrTbox[c]];
		no.text	= arrTbox[c];
		tbox[c]	= no;
	}
}

function valida()
{
	var	f =	document.datos;
	var	dat;

	dat=f.emailContent.value;
	if(dat.length == 0)
	{
		//old: Debe	introducir el Texto	del	Mensaje
		alert(\"".get_lang('PleaseEnterMessage')."\");
		f.emailContent.focus();
		f.emailContent.select();
		return false;	
	}
	
	f.submit();
	return true;
}

function selectAll(cbList,bSelect)
{
	if (cbList.length <	1) {
		//old: Debe	seleccionar	algn Alumno
		alert(\"".get_lang('PleaseSelectUsers')."\");
		return;
	}
	for	(var i=0; i<cbList.length; i++)	
		cbList[i].selected = cbList[i].checked = bSelect
}

function reverseAll(cbList)
{
	for	(var i=0; i<cbList.length; i++)
	{
		cbList[i].checked  = !(cbList[i].checked) 
		cbList[i].selected = !(cbList[i].selected)
	}
}
/* End ]]> */
</script>";

$nameTools = get_lang('Messages');

Display::display_header($nameTools,"Announcements");

/*
============================================================================== 
		Variable definitions
============================================================================== 
*/ 

$tbl_courseUser = Database::get_main_table(MAIN_COURSE_USER_TABLE);
$tbl_user       = Database::get_main_table(MAIN_USER_TABLE);
$tbl_group      = Database::get_course_group_table();
$tbl_groupUser  = Database::get_course_group_user_table();

/*
 * Various connection variables from the initialisation scripts
 */

$is_allowedToUse = $is_courseAdmin;
$courseCode      = $_course['official_code'];
$courseName      = $_course['name'        ];
$senderFirstName = $_user  ['firstName'   ];
$senderLastName  = $_user  ['lastName'    ];
$senderMail      = $_user  ['mail'        ];

/*
============================================================================== 
		Functions
============================================================================== 
*/ 
	
	
/*
============================================================================== 
		Main code
============================================================================== 
*/ 
if(is_allowed_to_edit())	// check teacher status
{
	api_display_tool_title($nameTools);

	/*----------------------------------------
		   DEFAULT DISPLAY SETTINGS
	 --------------------------------------*/

	$displayForm = true;

	// The commands	below will change these display settings if	they need it


	/*----------------------------------------
			SUBMIT ANNOUNCEMENT	COMMAND
	 --------------------------------------*/

	if ($submitAnnouncement) 
	{
		// SEND	EMAIL (OPTIONAL)
		// THIS	FUNCTION ADDED BY THOMAS MAY 2002
		// MODIFIED	CODE BY	MIGUEL ON 13/10/2003

		/******************************************************
		 * explode the values of	incorreo in	groups and users  *
		 *******************************************************/

		foreach($incorreo as $thisIncorreo)
		{
			list($type, $elmtId) = explode(':', $thisIncorreo);

			switch($type)
			{
				case 'GROUP':
					$groupIdList [] =$elmtId;
					break;

				case 'USER':
					$userIdList  [] =$elmtId;
					break;
			}
		}				// end while
		
		// SELECCIONAMOS	LOS	ALUMNOS	DE LOS DISTINTOS GRUPOS
		
		if ($groupIdList)
		{
			$groupIdList = "'".implode("', '",$groupIdList)."'";	//protect individual elements with surrounding quotes
			//older:
			//$groupIdList = implode(', ',$groupIdList);
			$sql = "SELECT user_id
					FROM ".$tbl_groupUser." user_group
					WHERE user_group.group_id IN (".$groupIdList.")
					";

			$groupMemberResult = api_sql_query($sql,__FILE__,__LINE__);
			
			if ($groupMemberResult)
			{
				while ($u = mysql_fetch_array($groupMemberResult))
				{
					$userIdList [] = $u['user_id']; // complete the user id list ...
				}
			}
		}


		if ($userIdList)
		{
			$userIdList = "'".implode("', '", array_unique($userIdList) )."'";	//protect individual elements with surrounding quotes

			$sql = "SELECT firstname firstName, lastname lastName, email
			        FROM ".$tbl_user." WHERE user_id IN (".$userIdList.")";

			$emailResult = api_sql_query($sql,__FILE__,__LINE__);

			if ($emailResult)
			{
				while ($e = mysql_fetch_array($emailResult))
				{
					if(eregi('^[0-9a-z_\.-]+@(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z][0-9a-z-]*[0-9a-z]\.)+[a-z]{2,3})$', $e['email'] ))
					{
						$emailList [] = $e['firstName'].' '.$e['lastName'].' <'.$e['email'].'>';
						//Display :: display_normal_message("send mail to user: " . $e[email]);
					}
					else
					{
						$invalidMailUserList [] = $e['firstName'].' '.$e['lastName'];
					}
				}
			}
		} // end if userIdList
		

		//well	send the differents mails
		
		 if( count($emailList) > 0)
		 {
			/* 
			 * Prepare	email
			 *
			 * Here	we are forming one large header	line
			 * Every header	must be	followed by	a \n except the	last
			 */

			$emailSubject = $courseCode." - ".$professorMessage;
		
			$emailHeaders = 'From:	'.$senderFirstName.' '.$senderLastName.' <'.$senderMail.">\n"
			               .'Reply-To:	'.$senderMail;

			$emailContent = stripslashes($emailContent);

			/*
			 * Send	email one by one to	avoid antispam
			 */

			$students='';  //MIGUEL: STUDENTS LIST FOR TEACHER MESSAGE
		
			foreach($emailList as $emailTo)
			{
				//AVOID ANTISPAM BY	VARYING STRING

				$emailBody = $courseName." \n"
							.$emailTo."\n\n"
							.$emailContent; 

				@api_send_mail($emailTo,	$emailSubject, $emailBody, $emailHeaders);		
			}
		 }

		$message = '<p>'.get_lang('MsgSent').'</p>';

		if ($invalidMailUserList && count($invalidMailUserList) > 0)
		{
			$messageUnvalid	= '<p>'
			                 .get_lang('On').'	'
			                 .count($emailList) + count($invalidMailUserList) .' '
			                 .get_lang('SelUser').',	'.$unvalid.' '.get_lang('Unvalid')
			                 .'<br/><small>('
			                 .implode(', ', $invalidMailUserList)
			                 .')</small>'
			                 .'</p>';

			$message .= $messageUnvalid;
		}

  }	// if $submit Announcement

//////////////////////////////////////////////////////////////////////////////


	/*----------------------------------------
				DISPLAY	ACTION MESSAGE
	 --------------------------------------*/

	if ($message)
	{

		echo	$message,
			"<br/>",
			"<br/>",
			"<a	href=\"".$_SERVER['PHP_SELF']."\">",get_lang('BackList'),"&nbsp;&gt;</a>",
			"<br/>";

		$displayForm = false;
	}

//////////////////////////////////////////////////////////////////////////////



/*
============================================================================== 
	DISPLAY FORM TO FILL AN ANNOUNCEMENT
	(USED FOR ADD AND MODIFY)
============================================================================== 
*/ 

	if ($displayForm ==	 true)
	{
		/*
		 * Get user list of this course
		 */
		$courseadmin_filter = $courseadmins_already_selected ? "AND cu.status != 1 " : "";
		
		$sql =	"SELECT u.lastname lastName, u.firstname firstName, u.user_id uid
		         FROM ".$tbl_user." u, ".$tbl_courseUser." cu
		         WHERE cu.course_code = '".$_cid."' 
		         AND cu.user_id = u.user_id $courseadmin_filter
		         ORDER BY u.firstname, u.lastname";

		$result	= api_sql_query($sql,__FILE__,__LINE__);

		if ($result)
		{
			while ($userData = mysql_fetch_array($result))
			{
				$userList [] = $userData;
			}
		}

		/*
		 * Get group list of this course
		 */
		$sql = "SELECT g.id, g.name, COUNT(gu.id) userNb 
		        FROM ".$tbl_group." AS g LEFT JOIN ".$tbl_groupUser." gu 
		        ON g.id = gu.group_id 
		        GROUP BY g.id";

		$groupSelect = api_sql_query($sql,__FILE__,__LINE__);

		while ($groupData = mysql_fetch_array($groupSelect))
		{
			$groupList [] = $groupData;
		}

		/*
		 * Create Form
		 */

		echo	get_lang('IntroText');

		echo	"<form method=\"post\" ",
			"action=\"".$_SERVER['PHP_SELF']."\" ",
			"name=\"datos\" ",
			"onSubmit=\"return valida();\">\n",

			"<table id=\"message\">",
			"<tr",
			"<td>",
			"<b>",get_lang('Userlist'),"</b><br/>",
			"<select name=\"nocorreo[]\" size=10 multiple>";				


		if ($groupList)
		{
			foreach($groupList as $thisGroup)
			{
				//Display :: display_normal_message("group " . $thisGroup[id] . $thisGroup[name]);
				echo	"<option value=\"GROUP:".$thisGroup[id]."\">",
					"G: ",$thisGroup['name']," - " . $thisGroup['userNb'] . " " . get_lang('Users') .
					"</option>";
			}

			echo	"<option value=\"\">",
				"---------------------------------------------------------",
				"</option>";
		}


		// display user list

		foreach($userList as $thisUser)
		{
			echo	"<option value=\"USER:",$thisUser['uid'],"\">",
				"",$thisUser['lastName']," ",$thisUser['firstName'],
				"</option>";
		}
		
			// WATCH OUT ! form elements are called by numbers "form.element[3]"... 
			// because select name contains "[]" causing a javascript 
			// element name problem List of selected users
		
		echo	"</select>",
			"</td>",
			"<td valign=\"middle\">",

			"<p><input	type=\"button\"	",
			"onClick=\"move(this.form.elements[0],this.form.elements[3])\" ",
			"value=\"   >>   \"></p>",

			"<p><input	type=\"button\"",
			"onClick=\"move(this.form.elements[3],this.form.elements[0])\" ", 
			"value=\"   <<   \"></p>",
			"</td>";

		if ($courseadmins_already_selected)
		{
		}
				
		echo	"<td>",
			"<b>",get_lang('SelectedUsers'),"</b><br/>",
			"<select name=\"incorreo[]\" ",
			"size=\"10\" multiple ";

		if ($courseadmins_already_selected)
		{
			$sql ="SELECT u.lastname lastName, u.firstname firstName, u.user_id uid
	        		 FROM ".$tbl_user." u, ".$tbl_courseUser." cu
			         WHERE cu.course_code = '".$_cid."' 
		        	 AND cu.user_id = u.user_id AND cu.status = 1
		    	     ORDER BY u.firstname, u.lastname";
			$result= api_sql_query($sql,__FILE__,__LINE__);
		
			if ($result)
			{
				while ($thisUser = mysql_fetch_array($result))
					echo	"<option value=\"USER:",$thisUser[uid],"\">",
							"",$thisUser[lastName]," ",$thisUser[firstName],
							"</option>";
			}
		}

		echo	"</select>",
			"</td>",
			"</tr>",

			"<tr>",
			"<td colspan=3>",
			"<b>",get_lang('MsgText'),"</b><br/>",
			"<textarea wrap=\"physical\" rows=\"7\"	cols=\"60\"	name=\"emailContent\"></textarea>",
			"</td>",
			"</tr>",

			"<tr>",
			"<td colspan=\"3\" align=\"center\">",
			"<input type=\"Submit\" name=\"submitAnnouncement\" ",
			"value=\"",get_lang('Submit'),"\" ",
			"onClick=\"selectAll(this.form.elements[3],true)\">",
			"</td>",
			"</tr>";
	}

echo	"</table>",
	"</form>";

} // end: teacher only

Display::display_footer();	
?> 