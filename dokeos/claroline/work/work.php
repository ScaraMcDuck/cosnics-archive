<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) Dokeos S.A.
	Copyright (c) Ghent University (UGent)
	Copyright (c) Universite catholique de Louvain (UCL)
	Copyright (c) Patrick Cool
	Copyright (c) Roan Embrechts (Vrije Universiteit Brussel)

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
 * 	STUDENT PUBLICATIONS MODULE
 *
 * Note: for a more advanced module, see the dropbox tool.
 * This one is easier with less options.
 * This tool is better used for publishing things,
 * sending in assignments is better in the dropbox.
 *
 * GOALS
 * *****
 * Allow student to quickly send documents immediately
 * visible on the course website.
 *
 * The script does 5 things:
 *
 * 	1. Upload documents
 * 	2. Give them a name
 * 	3. Modify data about documents
 * 	4. Delete link to documents and simultaneously remove them
 * 	5. Show documents list to students and visitors
 *
 * On the long run, the idea is to allow sending realvideo . Which means only
 * establish a correspondence between RealServer Content Path and the user's
 * documents path.
 *
 * All documents are sent to the address /$rootSys/$currentCourseID/document/
 * where $currentCourseID is the web directory for the course and $rootSys
 * usually /var/www/html
 *
 *	Modified by Patrick Cool, february 2004:
 *	Allow course managers to specify wether newly uploaded documents should 
 *	be visible or unvisible by default
 *	This is ideal for reviewing the uploaded documents before the document 
 *	is available for everyone.
 *	
 *	note: maybe the form to change the behaviour should go into the course 
 *	properties page?
 *	note 2: maybe a new field should be created in the course table for 
 *	this behaviour.
 *
 *	We now use the show_score field since this is not used.
 *
 *	@author Thomas, Hugues, Christophe - original version
 *	@author Patrick Cool, ability for course admins to specify wether uploaded documents
 *		are visible or invisible by default.
 *	@author Roan Embrechts, code refactoring and virtual course support
 *	@package dokeos.work
 * @todo refactor more code into functions
==============================================================================
 */

/*
==============================================================================
		INIT SECTION
==============================================================================
*/

$langFile = "work";
include('../inc/claro_init_global.inc.php');
$this_section=SECTION_COURSES;

api_protect_course_script();

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/ 
//the main_api.lib.php, database.lib.php and display.lib.php
//libraries are included by default
include_once(api_get_library_path() . "/course.lib.php");
include_once(api_get_library_path() . "/debug.lib.inc.php");
include_once(api_get_library_path() . "/events.lib.inc.php");

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
$tool_name = get_lang(TOOL_STUDENTPUBLICATION);
$main_course_table = Database::get_main_table(MAIN_COURSE_TABLE);

$user_id = api_get_user_id();
$course_code = $_course['sysCode'];
$is_course_member = CourseManager::is_user_subscribed_in_real_or_linked_course($user_id, $course_code);

$work_table = Database::get_course_table(STUDENT_PUBLICATION_TABLE);
$currentCourseRepositorySys =  api_get_path(SYS_COURSE_PATH) . $_course["path"]."/";
$currentCourseRepositoryWeb =  api_get_path(WEB_COURSE_PATH) . $_course["path"]."/";
$currentUserFirstName       = $_user['firstName'];
$currentUserLastName        = $_user['lastName'];

$authors = $_REQUEST['authors'];
$delete = $_REQUEST['delete'];
$description = $_REQUEST['description'];
$display_tool_options = $_REQUEST['display_tool_options'];
$display_upload_form = $_REQUEST['display_upload_form'];
$edit = $_REQUEST['edit'];
$make_invisible = $_REQUEST['make_invisible'];
$make_visible = $_REQUEST['make_visible'];
$origin = $_REQUEST['origin'];
$submitGroupWorkUrl = $_REQUEST['submitGroupWorkUrl'];
$submitWork = $_REQUEST['submitWork'];
$title = $_REQUEST['title'];
$uploadvisibledisabled = $_REQUEST['uploadvisibledisabled'];

//new roles and rights system for 1.7
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_STUDENTPUBLICATION);
$is_allowed_to_edit = RolesRights::is_allowed($role_id, EDIT_RIGHT, $location_id);
$is_allowed = RolesRights::is_allowed_which_rights($role_id, $location_id);

/*
-----------------------------------------------------------
	Configuration settings
-----------------------------------------------------------
*/
$link_target_parameter = ""; //or e.g. "target=\"_blank\"";
$always_show_tool_options = false;
$always_show_upload_form = false;
if ($always_show_tool_options) $display_tool_options = true;
if ($always_show_upload_form) $display_upload_form = true;

/*
-----------------------------------------------------------
	More init stuff
-----------------------------------------------------------
*/

if(isset($_POST['cancelForm']) && !empty($_POST['cancelForm']))
{
	header('Location: '.$_SERVER['PHP_SELF']."?origin=$origin");
	exit();
}

if ($submitWork || $submitGroupWorkUrl)
{
	// these libraries are only used for upload purpose
	// so we only include them when necessary
	include_once(api_get_include_path()."/lib/fileUpload.lib.php");
	include_once(api_get_include_path()."/lib/fileDisplay.lib.php"); // need format_url function
}

// If the POST's size exceeds 8M (default value in php.ini) the $_POST array is emptied
// If that case happens, we set $submitWork to 1 to allow displaying of the error message
// The redirection with header() is needed to avoid apache to show an error page on the next request
if($_SERVER['REQUEST_METHOD'] == 'POST' && !sizeof($_POST))
{
	if(strstr($_SERVER['REQUEST_URI'],'?'))
	{
		header('Location: '.$_SERVER['REQUEST_URI'].'&submitWork=1');
		exit();
	}
	else
	{
		header('Location: '.$_SERVER['REQUEST_URI'].'?submitWork=1');
		exit();
	}
}

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
if ($origin != 'learnpath')
{
	Display::display_header($tool_name,"Work");
}
else
{
	//we are in the learnpath tool
	?> <link rel="stylesheet" type="text/css" href="<?php echo api_get_path(WEB_CODE_PATH); ?>css/default.css"> <?php
}

event_access_tool(TOOL_STUDENTPUBLICATION); //for statistics
api_display_tool_title($tool_name);

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

function display_action_links($always_show_tool_options, $always_show_upload_form, $is_allowed)
{
	$display_output = "";
	if (! $always_show_upload_form && $is_allowed[ADD_RIGHT])
	{
		$display_output .= "<li><strong>" . "<a href=\"".$_SERVER['PHP_SELF']."?display_upload_form=true\">". get_lang("UploadADocument") . "</strong></a></li>";
	}
	if (! $always_show_tool_options && $is_allowed[EDIT_RIGHT] )
	{
		$display_output .=	"<li><strong>" . "<a href=\"".$_SERVER['PHP_SELF']."?display_tool_options=true\">" . get_lang("EditToolOptions") . "</a>" . "</strong></a></li>";
	}
	
	if ($display_output != "") echo "<ul>$display_output</ul>";
}

/**
* Displays all options for this tool.
* These are
* - make all files visible / invisible
* - set the default visibility of uploaded files
*
* @param $uploadvisibledisabled
* @param $origin
*/
function display_tool_options($uploadvisibledisabled, $origin, $is_allowed)
{
	$work_table = Database::get_course_table(STUDENT_PUBLICATION_TABLE);
	
	if (! $is_allowed[EDIT_RIGHT]) return;

	echo	"<table cellpadding=\"5\" cellspacing=\"2\" border=\"0\">\n";
		
	echo	"<tr>\n",
			"<td colspan=\"2\">\n",
	
			"<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" bordercolor\"gray\">\n",
			"<tr>\n",
			"<td>",
			get_lang('AllFiles').": ";
			
			if ($is_allowed[DELETE_RIGHT]) echo
			"<a href=\"".$_SERVER['PHP_SELF']."?origin=$origin&delete=all\" ",
			"onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang('ConfirmYourChoice')))."')) return false;\">",
			"<img src=\"../img/delete.gif\" border=\"0\" alt=\"".get_lang('Delete')."\" align=\"absmiddle\">",
			"</a>",
			"&nbsp;";

	$sql_query = "SHOW COLUMNS FROM ".$work_table." LIKE 'accepted'";
	$sql_result = api_sql_query($sql_query,__FILE__,__LINE__);

	if ($sql_result)
	{
		$columnStatus = mysql_fetch_array($sql_result);

		if ($columnStatus['Default'] == 1)
		{
			echo	"<a href=\"".$_SERVER['PHP_SELF']."?origin=$origin&make_invisible=all\">",
					"<img src=\"../img/visible.gif\" border=\"0\" alt=\"".get_lang('Invisible')."\" align=\"absmiddle\">",
					"</a>\n";
		}
		else
		{
			echo	"<a href=\"".$_SERVER['PHP_SELF']."?origin=$origin&make_visible=all\">",
					"<img src=\"../img/invisible.gif\" border=\"0\" alt=\"".get_lang('Visible')."\" align=\"absmiddle\">",
					"</a>\n";
		}
	}

	echo "</td></tr>";

	display_default_visibility_form($uploadvisibledisabled, $origin);
	
	echo "</table>";
	echo "</td></tr>";
	echo "</table>";
}

/**
* Displays the form where course admins can specify wether uploaded documents
* are visible or invisible by default.
*
* @param $uploadvisibledisabled
* @param $origin
*/
function display_default_visibility_form($uploadvisibledisabled, $origin)
{
	?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?origin=$origin"; ?>">
	<tr><td align="left">
		<strong><?php echo get_lang("_default_upload"); ?><br></strong>
		<input class="checkbox" type="radio" name="uploadvisibledisabled" value="0"
			<?php if($uploadvisibledisabled==0) echo "checked";  ?>>
		<?php echo get_lang("_new_visible");?><br>
		<input class="checkbox" type="radio" name="uploadvisibledisabled" value="1"
			<?php if($uploadvisibledisabled==1) echo "checked";  ?>>
		<?php echo get_lang("_new_unvisible"); ?><br>
		<input type="submit" name="changeProperties" value="<?php echo get_lang("Ok"); ?>">
	</td></tr>
	</form>
	<?php
}

/**
* Display the list of student publications, taking into account the user status
*
* @param $currentCourseRepositoryWeb, the web location of the course folder
* @param $link_target_parameter - should there be a target parameter for the links
* @param $dateFormatLong - date format
* @param $origin - typically empty or 'learnpath'
*/
function display_student_publications_list($currentCourseRepositoryWeb, $link_target_parameter, $dateFormatLong, $origin, $is_allowed)
{
	//init
	$work_table = Database::get_course_table(STUDENT_PUBLICATION_TABLE);
	$publications_list = array();
	$sort_params = array();
	if( isset($_GET['column']))
	{
		$sort_params[] = 'column='.$_GET['column'];
	}
	if( isset($_GET['page_nr']))
	{
		$sort_params[] = 'page_nr='.$_GET['page_nr'];
	}
	if( isset($_GET['per_page']))
	{
		$sort_params[] = 'per_page='.$_GET['per_page'];
	}
	if( isset($_GET['direction']))
	{
		$sort_params[] = 'direction='.$_GET['direction'];
	}	
	$sort_params = implode('&amp;',$sort_params);
			
	//Get list from database
	$sql_get_publications_list =	"SELECT *
									FROM  ".$work_table."
		                 			ORDER BY id";
	$sql_result = api_sql_query($sql_get_publications_list,__FILE__,__LINE__);
	
	$table_header[] = array(get_lang('Title'),true);
	$table_header[] = array(get_lang('Description'),true);
	$table_header[] = array(get_lang('Authors'),true);
	$table_header[] = array(get_lang('Date'),true);
	if( $is_allowed[EDIT_RIGHT])
	{
		$table_header[] = array(get_lang('Modify'),true);
	}
	$table_data = array();
	while( $work = mysql_fetch_object($sql_result))
	{
		if( $work->accepted == '1' || $is_allowed[EDIT_RIGHT])
		{
			$row = array();
			if($work->accepted == '0')
			{	
				$class='class="invisible"';
			}
			else 
			{
				$class='';
			}
			$url = implode("/", array_map("rawurlencode", explode("/", $work->url)));
			$row[] = '<a href="'.$currentCourseRepositoryWeb.$url.'"'.$class.'>'.$work->title.'</a>';
			$row[] = $work->description;
			$row[] = $work->author;
			$row[] = $work->sent_date;	
			if( $is_allowed[EDIT_RIGHT])
			{
				if($work->accepted == '1')	
				{
					$action = '<a href="'.$_SERVER['PHP_SELF'].'?origin='.$origin.'&make_invisible='.$work->id.'&amp;'.$sort_params.'"><img src="../img/visible.gif" alt="'.get_lang('Invisible').'"></a>';
				}
				else
				{
					$action = '<a href="'.$_SERVER['PHP_SELF'].'?origin='.$origin.'&make_visible='.$work->id.'&amp;'.$sort_params.'"><img src="../img/invisible.gif" alt="'.get_lang('Visible').'"></a>';
				}
				$action .= '<a href="'.$_SERVER['PHP_SELF'].'?origin='.$origin.'&edit='.$work->id.'"><img src="../img/edit.gif" alt="'.get_lang('Modify').'"></a>';
				if ($is_allowed[DELETE_RIGHT])
				{
					$action .= '<a href="'.$_SERVER['PHP_SELF'].'?origin='.$origin.'&delete='.$work->id.'" onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang('ConfirmYourChoice')))."'".')) return false;"><img src="../img/delete.gif" alt="'.get_lang('WorkDelete').'"></a>';
				}
				$row[] = $action;
			}
			$table_data[] = $row;
		}
	}
	if( count($table_data) > 0)
	{
		Display::display_sortable_table($table_header,$table_data);
	}
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/ 

if (isset($_POST['changeProperties']))
{
	$query="UPDATE ".$main_course_table." SET show_score='".$uploadvisibledisabled."' WHERE code='".$_course['sysCode']."'";
	api_sql_query($query,__FILE__,__LINE__);

	$_course['show_score']=$uploadvisibledisabled;
}
else
{
	$query="SELECT * FROM ".$main_course_table." WHERE code=\"".$_course['sysCode']."\"";
	$result=api_sql_query($query,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$uploadvisibledisabled = $row["show_score"];
}


/*
-----------------------------------------------------------
	Introduction section
	(editable by course admins)
-----------------------------------------------------------
*/
Display::display_introduction_section(TOOL_STUDENTPUBLICATION, $is_allowed);

/*
-----------------------------------------------------------
	COMMANDS SECTION (reserved for course administrator)
-----------------------------------------------------------
*/ 
if ($is_allowed_to_edit)
{
	/*-------------------------------------------
				DELETE WORK COMMAND
	-----------------------------------------*/
	if ($delete)
	{	
		if ($delete == "all")
		{
			$queryString1 = "SELECT url FROM ".$work_table."";
			$queryString2 = "DELETE FROM  ".$work_table."";
		}
		else
		{
			$queryString1 = "SELECT url FROM  ".$work_table."  WHERE id = '$delete'";
			$queryString2 = "DELETE FROM  ".$work_table."  WHERE id='$delete'";
		}

		$result1 = api_sql_query($queryString1,__FILE__,__LINE__);
		$result2 = api_sql_query($queryString2,__FILE__,__LINE__);

		if ($result1)
		{
			while ($thisUrl = mysql_fetch_array($result1))
			{
				// check the url really points to a file in the work area
				// (some work links can come from groups area...)

				if (substr (dirname($thisUrl['url']), -4) == "work")
				{
					@unlink($currentCourseRepositorySys."work/".$thisWork);
				}
			}
		}
	}

	/*-------------------------------------------
	           EDIT COMMAND WORK COMMAND
	  -----------------------------------------*/

	if ($edit)
	{
		$sql    = "SELECT * FROM  ".$work_table."  WHERE id='".$edit."'";
		$result = api_sql_query($sql,__FILE__,__LINE__);

		if ($result)
		{
			$row = mysql_fetch_array($result);

			$workTitle       = $row ['title'      ];
			$workAuthor      = $row ['author'     ];
			$workDescription = $row ['description'];
			$workUrl         = $row ['url'        ];
		}
	}



	/*-------------------------------------------
		MAKE INVISIBLE WORK COMMAND
	  -----------------------------------------*/

	if ($make_invisible)
	{
		if ($make_invisible == "all")
		{
			$sql = "ALTER TABLE ".$work_table."
			        CHANGE accepted accepted TINYINT(1) DEFAULT '0'";

			api_sql_query($sql,__FILE__,__LINE__);

			$sql = "UPDATE  ".$work_table."
			        SET accepted = 0";

			api_sql_query($sql,__FILE__,__LINE__);
		}
		else
		{
			$sql = "UPDATE  ".$work_table."
			        SET accepted = 0
					WHERE id = '".$make_invisible."'";

			api_sql_query($sql,__FILE__,__LINE__);
		}
	}



	/*-------------------------------------------
		MAKE VISIBLE WORK COMMAND
	  -----------------------------------------*/

	if ($make_visible)
	{
		if ($make_visible == "all")
		{
			$sql = "ALTER TABLE  ".$work_table."
			        CHANGE accepted accepted TINYINT(1) DEFAULT '1'";

			api_sql_query($sql,__FILE__,__LINE__);

			$sql = "UPDATE  ".$work_table."
			        SET accepted = 1";

			api_sql_query($sql,__FILE__,__LINE__);

		}
		else
		{
			$sql = "UPDATE  ".$work_table."
			        SET accepted = 1
					WHERE id = '".$make_visible."'";

			api_sql_query($sql,__FILE__,__LINE__);
		}
	}

}

/*
==============================================================================
		FORM SUBMIT PROCEDURE
==============================================================================
*/ 

$error_message="";

if($submitWork && $is_course_member)
{
	if($_FILES['file']['size'])
	{
		// exemple :
		// $urlAppend="/cvs130/Dokeos150";
		// $rootSys="/var/www/html/cvs130/Dokeos150/";

		$updir           = $currentCourseRepositorySys.'work/'; //directory path to upload

		// Try to add an extension to the file if it has'nt one
		$new_file_name = add_ext_on_mime(stripslashes($_FILES['file']['name']),$_FILES['file']['type']);

		// Replace dangerous characters
		$new_file_name = replace_dangerous_char($new_file_name,'strict');

		// Transform any .php file in .phps fo security
		$new_file_name = php2phps($new_file_name);

		if( ! $title )
		{
			$title = $_FILES['file']['name'];
		}

		if ( ! $authors)
		{
			$authors = $currentUserFirstName." ".$currentUserLastName;
		}

		// compose a unique file name to avoid any conflict

		$new_file_name = uniqid('').$new_file_name;

		move_uploaded_file($_FILES['file']['tmp_name'],$updir.$new_file_name);

		$url = "work/".$new_file_name;

		$result = api_sql_query("SHOW FIELDS FROM ".$work_table." LIKE 'sent_date'",__FILE__,__LINE__);

		if(!mysql_num_rows($result))
		{
			api_sql_query("ALTER TABLE ".$work_table." ADD sent_date DATETIME NOT NULL");
		}

		$sql_add_publication = "INSERT INTO ".$work_table."
		               SET url         = '".mysql_real_escape_string($url)."',
					       title       = '".mysql_real_escape_string($title)."',
		                   description = '".mysql_real_escape_string($description)."',
		                   author      = '".mysql_real_escape_string($authors)."',
						   active		= '".$active."',
						   accepted		= '".(!$uploadvisibledisabled)."',
						   sent_date			= NOW()";

		api_sql_query($sql_add_publication,__FILE__,__LINE__);

        $Id = mysql_insert_id();
		$succeed = true;
	}

	/*
	 * SPECIAL CASE ! For a work coming from another area (i.e. groups)
	 */

	elseif ($newWorkUrl)
	{

		$url = str_replace('../../'.$_course['path'].'/','',$newWorkUrl);


		if( ! $title )
		{
			$title = basename($workUrl);
		}

		$result = api_sql_query("SHOW FIELDS FROM ".$work_table." LIKE 'sent_date'",__FILE__,__LINE__);

		if(!mysql_num_rows($result))
		{
			api_sql_query("ALTER TABLE ".$work_table." ADD sent_date DATETIME NOT NULL");
		}

		$sql = "INSERT INTO  ".$work_table."
		        SET url         = '".$url."',
		            title       = '".$title."',
		            description = '".$description."',
		            author      = '".$authors."',
		            sent_date     = NOW()";

		api_sql_query($sql,__FILE__,__LINE__);

		$insertId = mysql_insert_id();
		$succeed = true;
	}

	/*
	 * SPECIAL CASE ! For a work edited
	 */

	elseif ($id && $is_allowed_to_edit)
	{
		if( ! $title )
		{
			$title = basename($newWorkUrl);
		}

		$sql = "UPDATE  ".$work_table."
		        SET	title       = '".$title."',
		            description = '".$description."',
		            author      = '".$authors."'
		        WHERE id        = '".$id."'";

		api_sql_query($sql,__FILE__,__LINE__);

        $insertId = $id;
		$succeed = true;
	}
	else
	{
		$error_message = get_lang('TooBig');
	}
}

if ($submitWork && $succeed)
{
	$message = get_lang('DocAdd');
    if ($uploadvisibledisabled && !$is_allowed_to_edit)
	{
		$message .= "<br>".get_lang('_doc_unvisible')."<br>";
	}

	//stats
	if(!$Id) { $Id = $insertId; }
    event_upload($Id);
	$submit_success_message	= $message . "<br>\n";
	Display::display_normal_message($submit_success_message);
}

{
	/*=======================================
		 Display links to upload form and tool options
	  =======================================*/
	
	display_action_links($always_show_tool_options, $always_show_upload_form, $is_allowed);

	/*=======================================
		 Display form to upload document
	  =======================================*/
	  
	//if($is_course_member)
	if ($is_allowed[ADD_RIGHT])
	{
		if ($display_upload_form || $edit)
		{
			echo	"<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."?origin=$origin\" enctype=\"multipart/form-data\" >\n",
					"<table>\n";
	
			if(!empty($error_message)) Display::display_error_message($error_message);
	
			if ($submitGroupWorkUrl) // For user comming from group space to publish his work
			{
				$realUrl = str_replace ($rootSys, $rootWeb, str_replace("\\", "/", realpath($submitGroupWorkUrl) ) ) ;
	
				echo	"<tr>\n",
	
						"<td align=\"right\">",
						"<input type=\"hidden\" name=\"newWorkUrl\" value=\"",$submitGroupWorkUrl,"\">",
						get_lang("Document")," : ",
						"</td>\n",
						"<td align=\"right\">",
						"<a href=\"",format_url($submitGroupWorkUrl),"\">",$realUrl,"</a>",
						"</td>\n",
	
						"</tr>\n";
			}
			elseif ($edit && $is_allowed_to_edit)
			{
				$workUrl = $currentCourseRepositoryWeb.$workUrl;
	
				echo	"<tr>\n",
	
						"<td>",
						"<input type=\"hidden\" name=\"id\" value=\"",$edit,"\">\n",
						get_lang('Document')," : ",
						"</td>\n",
	
						"<td>",
						"<a href=\"",$workUrl,"\">",$workUrl,"</a>",
						"</td>\n",
	
						"</tr>\n";
			}
			else // else standard upload option
			{
				echo	"<tr>\n",
	
						"<td  align=\"right\"><strong>",
						get_lang("DownloadFile"),"</strong>&nbsp;&nbsp;",
						"</td>\n",
	
						"<td>",
						"<input type=\"file\" name=\"file\" size=\"20\">",
						"</td>\n",
	
						"</tr>\n";
			}
	
			if(empty($authors))
			{
				$authors=$_user['lastName']." ".$_user['firstName'];
			}
	
			echo	"<tr>\n",
	
					"<td  align=\"right\"><strong>",
					get_lang("TitleWork"),"</strong>&nbsp;&nbsp;",
					"</td>\n",
	
					"<td>",
					"<input type=\"text\" name=\"title\" value=\"",($edit?htmlentities(stripslashes($workTitle)):htmlentities(stripslashes($title))),"\" size=\"30\">",
					"</td>\n",
	
					"</tr>\n",
	
					"<tr>\n",
	
					"<td valign=\"top\"  align=\"right\"><strong>",
					get_lang("Authors")."</strong>&nbsp;&nbsp;",
					"</td>\n",
	
					"<td>",
					"<input type=\"text\" name=\"authors\" value=\"",($edit?htmlentities(stripslashes($workAuthor)):htmlentities(stripslashes($authors))),"\" size=\"30\">\n",
					"</td>\n",
	
					"</tr>\n",
	
					"<tr>\n",
	
					"<td valign=\"top\"  align=\"right\">",
					get_lang("Description"),"&nbsp;&nbsp;",
					"</td>\n",
	
					"<td>",
					"<textarea name=\"description\" cols=\"30\" rows=\"3\">",
					($edit?htmlentities(stripslashes($workDescription)):htmlentities(stripslashes($description))),
					"</textarea>",
					"<input type=\"hidden\" name=\"active\" value=\"1\">",
					"<input type=\"hidden\" name=\"accepted\" value=\"1\">",
					"</td>\n",
	
					"</tr>\n",
	
					"<tr>\n",
	
					"<td></td>",
	
					"<td>",
					"<input type=\"submit\" name=\"submitWork\" value=\"".get_lang('Ok')."\">";
	
			if($submitWork || $edit)
			{
				echo "&nbsp;&nbsp;<input type=\"submit\" name=\"cancelForm\" value=\"".get_lang('Cancel')."\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang('ConfirmYourChoice')))."')) return false;\">";
			}
	
			echo	"</td>\n",
	
					"</tr>\n",
	
					"</table>\n",
	
					"</form>\n",
	
					"<p>&nbsp;</p>";
		}
	}
	else
	{
		//the user is not registered in this course
		echo "<p style=\"font-weight:bold\">" . get_lang("MustBeRegisteredUser") . "</p>";
	}

/*
==============================================================================
		Display of tool options
==============================================================================
*/

	if ($display_tool_options)
	{
		display_tool_options($uploadvisibledisabled, $origin, $is_allowed);
	}

/*
==============================================================================
		Display list of student publications
==============================================================================
*/

	/*if ( ! $id )*/ display_student_publications_list($currentCourseRepositoryWeb, $link_target_parameter, $dateFormatLong, $origin, $is_allowed);
}

/*
==============================================================================
		Footer 
==============================================================================
*/ 
if ($origin != 'learnpath')
{
	//we are not in the learning path tool
	Display::display_footer();
}
?>