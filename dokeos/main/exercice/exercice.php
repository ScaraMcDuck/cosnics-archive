<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
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
*	EXERCISE LIST
*
*	This script shows the list of exercises for administrators and students.
*
*	@author Olivier Brouckaert, original author
*	@author Denes Nagy, HotPotatoes integration
*	@author Wolfgang Schneider, code/html cleanup
*	@package dokeos.exercise
==============================================================================
*/

api_use_lang_files('exercice');

require_once('../inc/claro_init_global.inc.php');
$this_section=SECTION_COURSES;
api_protect_course_script();

// Roles and rights system
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_QUIZ);
$is_allowed = RolesRights::is_allowed_which_rights($role_id, $location_id);
//block users without view right
RolesRights::protect_location($role_id, $location_id);

$show=(isset($_GET['show']) && $_GET['show'] == 'result')?'result':'test'; // moved down to fix bug: http://www.dokeos.com/forum/viewtopic.php?p=18609#18609

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
require_once('exercise.class.php');
require_once('question.class.php');
require_once('answer.class.php');
require_once(api_get_library_path().'/fileManage.lib.php');
require_once(api_get_library_path().'/fileUpload.lib.php');
require_once($rootSys.'main/exercice/hotpotatoes.lib.php');

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
$is_allowedToEdit = api_is_allowed_to_edit();

$TBL_USER          	    = Database::get_main_table(MAIN_USER_TABLE);
$TBL_DOCUMENT          	= Database::get_course_table(DOCUMENT_TABLE);
$TBL_ITEM_PROPERTY      = Database::get_course_table(ITEM_PROPERTY_TABLE);
$TBL_EXERCICE_QUESTION		= Database::get_course_table(QUIZ_TEST_QUESTION_TABLE);
$TBL_EXERCICES								= Database::get_course_table(QUIZ_TEST_TABLE);
$TBL_QUESTIONS								= Database::get_course_table(QUIZ_QUESTION_TABLE);
$TBL_TRACK_EXERCICES   	= $statsDbName."`.`track_e_exercices";
$TBL_TRACK_HOTPOTATOES  = $statsDbName."`.`track_e_hotpotatoes";

// document path
$documentPath= api_get_path(SYS_COURSE_PATH).$_course['path']."/document";
// picture path
$picturePath=$documentPath.'/images';
// audio path
$audioPath=$documentPath.'/audio';

// hotpotatoes
$uploadPath = "/HotPotatoes_files";
$exercicePath = $_SERVER['PHP_SELF'];
$exfile = explode('/',$exercicePath);
$exfile = strtolower($exfile[sizeof($exfile)-1]);
$exercicePath = substr($exercicePath,0,strpos($exercicePath,$exfile));
$exercicePath = $exercicePath."exercice.php";

// maximum number of exercises on a same page
$limitExPage=50;

// Clear the exercise session
if(isset($_SESSION['objExercise']))		{ api_session_unregister('objExercise');		}
if(isset($_SESSION['objQuestion']))		{ api_session_unregister('objQuestion');		}
if(isset($_SESSION['objAnswer']))		{ api_session_unregister('objAnswer');		}
if(isset($_SESSION['questionList']))	{ api_session_unregister('questionList');	}
if(isset($_SESSION['exerciseResult']))	{ api_session_unregister('exerciseResult');	}

//general POST/GET/SESSION/COOKIES parameters recovery
if ( empty ( $origin ) ) {
    $origin     = $_REQUEST['origin'];
}
if ( empty ($choice ) ) {
    $choice     = $_REQUEST['choice'];
}
if ( empty ( $hpchoice ) ) {
    $hpchoice   = $_REQUEST['hpchoice'];
}
if ( empty ($exerciseId ) ) {
    $exerciseId = mysql_real_escape_string($_REQUEST['exerciseId']);
}
if ( empty ( $file ) ) {
    $hpchoice   = mysql_real_escape_string($_REQUEST['file']);
}
$learnpath_id = mysql_real_escape_string($_REQUEST['learnpath_id']);
$learnpath_item_id = mysql_real_escape_string($_REQUEST['learnpath_item_id']);
$page = mysql_real_escape_string($_REQUEST['page']);

if($origin == 'learnpath'){
	$show = 'result';
}
$htmlHeadXtra[]='<style type="text/css">
/*<![CDATA[*/
a.invisible
{
	color: #999999;
}

a.invisible:visited
{
	color: #999999;
}

a.invisible:active
{
	color: #999999;
}

a.invisible:hover
{
	color: #999999;
}
/*]]>*/
</style>';

$nameTools=get_lang('Exercices');

if ($origin != 'learnpath') { //so we are not in learnpath tool
	Display::display_header($nameTools,"Exercise");
} else {
	?> <link rel="stylesheet" type="text/css" href="<?php echo api_get_path(WEB_CODE_PATH); ?>css/default.css"/>

<?php
}

// used for stats
include_once(api_get_library_path().'/events.lib.inc.php');

event_access_tool(TOOL_QUIZ);

// need functions of statsutils lib to display previous exercices scores
include_once(api_get_library_path().'/statsUtils.lib.inc.php');

if($is_allowed[EDIT_RIGHT] || $is_allowed[ADD_RIGHT])
{
	include_once(api_get_library_path().'/fileUpload.lib.php');
	if(!is_dir($audioPath))
	{
		if(is_file($audioPath))
		{
			@unlink($audioPath);
		}

		@mkdir($audioPath);

		//$query="INSERT INTO $TBL_DOCUMENT (path,filetype) VALUES('".str_replace($documentPath,'',$audioPath)."','folder')";
		//api_sql_query($query,__FILE__,__LINE__);
		//$id = Database::get_last_insert_id();
		$id = add_document($_course,str_replace($documentPath,'',$audioPath),'folder',0,'Audio');
		//$time = time();
		//$time = date("Y-m-d H:i:s", $time);

		//$query = "INSERT INTO $TBL_ITEM_PROPERTY (tool, ref, insert_user_id, insert_date, lastedit_type) VALUES ('".TOOL_DOCUMENT."', $id, $_uid, '$time', 'DocumentAdded' )";
		//api_sql_query($query,__FILE__,__LINE__);
		api_item_property_update($_course,TOOL_DOCUMENT,$id,'FolderCreated',$_uid);
	}

	if(!is_dir($picturePath))
	{
		if(is_file($picturePath))
		{
			@unlink($picturePath);
		}

		@mkdir($picturePath);

		//$query="INSERT INTO $TBL_DOCUMENT (path, filetype) VALUES('".str_replace($documentPath,'',$picturePath)."','folder')";
		//api_sql_query($query,__FILE__,__LINE__);
		//$id = Database::get_last_insert_id();
		$id = add_document($_course,str_replace($documentPath,'',$picturePath),'folder',0,'Pictures');
		//$time = time();
		//$time = date("Y-m-d H:i:s", $time);

		//$query = "INSERT INTO $TBL_ITEM_PROPERTY (tool, ref, insert_user_id, insert_date, lastedit_type) VALUES ('".TOOL_DOCUMENT."', $id, $_uid, '$time', 'DocumentAdded' )";
		//api_sql_query($query,__FILE__,__LINE__);
		api_item_property_update($_course,TOOL_DOCUMENT,$id,'FolderCreated',$_uid);
	}
}
if($origin != 'learnpath'){
	api_display_tool_title($nameTools);
}

//Introduction section
Display::display_introduction_section(TOOL_QUIZ, $is_allowed);

// defines answer type for previous versions of Claroline, may be removed in Claroline 1.5
$sql="UPDATE $TBL_QUESTIONS SET position='1',type='2' WHERE position IS NULL OR position<'1' OR type='0'";
api_sql_query($sql,__FILE__,__LINE__);

// selects $limitExPage exercises at the same time
$from=$page*$limitExPage;
//	$sql="SELECT id,title,type,active FROM $TBL_EXERCICES ORDER BY title LIMIT $from,".($limitExPage+1);
//	$result=api_sql_query($sql,__FILE__,__LINE__);
$sql="SELECT count(id) FROM $TBL_EXERCICES";
$res = api_sql_query($sql,__FILE__,__LINE__);
list($nbrexerc) = mysql_fetch_row($res);

HotPotGCt($documentPath,1,$_uid);

// only for administrator

if($is_allowed[EDIT_RIGHT] || $is_allowed[ADD_RIGHT] || $is_allowed[DELETE_RIGHT])
{
	if(!empty($choice))
	{
		// construction of Exercise
		$objExerciseTmp=new Exercise();

		if($objExerciseTmp->read($exerciseId))
		{
			switch($choice)
			{
				case 'delete':	// deletes an exercise
								$objExerciseTmp->delete();

								break;
				case 'enable':  // enables an exercise
								$objExerciseTmp->enable();
								$objExerciseTmp->save();

								// "WHAT'S NEW" notification: update table item_property (previously last_tooledit)
								api_item_property_update($_course, TOOL_QUIZ, $exerciseId, "QuizAdded", $_uid);

								break;
				case 'disable': // disables an exercise
								$objExerciseTmp->disable();
								$objExerciseTmp->save();

								break;
			}
		}

		// destruction of Exercise
		unset($objExerciseTmp);
	}

	//$sql="SELECT id,title,type,active FROM $TBL_EXERCICES ORDER BY title LIMIT $from,".($limitExPage+1);
	//$result=api_sql_query($sql,__FILE__,__LINE__);


	if(!empty($hpchoice))
	{
		switch($hpchoice)
		{
				case 'delete':	// deletes an exercise
							$imgparams = array();
							$imgcount = 0;
							GetImgParams($file,$documentPath,$imgparams,$imgcount);
							$fld = GetFolderName($file);
							for($i=0;$i < $imgcount;$i++)
							{
									my_delete($documentPath.$uploadPath."/".$fld."/".$imgparams[$i]);
									update_db_info("delete", $uploadPath."/".$fld."/".$imgparams[$i]);
							}

							if ( my_delete($documentPath.$file))
							{
								update_db_info("delete", $file);
							}
							my_delete($documentPath.$uploadPath."/".$fld."/");
							break;
				case 'enable':  // enables an exercise
					$newVisibilityStatus = "1"; //"visible"
                    $query = "SELECT id FROM $TBL_DOCUMENT WHERE path='$file'";
                    $res = api_sql_query($query,__FILE__,__LINE__);
                    $row = Database::fetch_array($res, 'ASSOC');
                    api_item_property_update($_course, TOOL_DOCUMENT, $row['id'], 'visible', $_uid);
                    //$dialogBox = get_lang('ViMod');

							break;
				case 'disable': // disables an exercise
					$newVisibilityStatus = "0"; //"invisible"
                    $query = "SELECT id FROM $TBL_DOCUMENT WHERE path='$file'";
                    $res = api_sql_query($query,__FILE__,__LINE__);
                    $row = Database::fetch_array($res, 'ASSOC');
                    api_item_property_update($_course, TOOL_DOCUMENT, $row['id'], 'invisible', $_uid);
					#$query = "UPDATE $TBL_DOCUMENT SET visibility='$newVisibilityStatus' WHERE path=\"".$file."\""; //added by Toon
					#api_sql_query($query,__FILE__,__LINE__);
					//$dialogBox = get_lang('ViMod');

								break;
		}
	}

	if($show == 'test')
	{
		$sql="SELECT id,title,type,active FROM $TBL_EXERCICES ORDER BY title LIMIT $from,".($limitExPage+1);
		$result=api_sql_query($sql,__FILE__,__LINE__);
	}
}
// only for students
elseif($show == 'test')
{
	$sql="SELECT id,title,type FROM $TBL_EXERCICES WHERE active='1' ORDER BY title LIMIT $from,".($limitExPage+1);
	$result=api_sql_query($sql,__FILE__,__LINE__);
}


if($show == 'test'){

	//error_log('Show == test',0);
	$nbrExercises=mysql_num_rows($result);

	echo "<table border=\"0\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" width=\"100%\">",
		"<tr>";

	if (($is_allowed[ADD_RIGHT]) and ($origin != 'learnpath'))
	{
		//error_log('is_allowedToEdit and origin<> learnpath',0);
		echo "<td width=\"50%\" nowrap=\"nowrap\">",
			"<img src=\"../img/quiz.gif\" alt=\"new test\" valign=\"ABSMIDDLE\">&nbsp;<a href=\"admin.php\">".get_lang("NewEx")."</a> | ",
			"<img src=\"../img/quiz_na.gif\" alt=\"new test\" valign=\"ABSMIDDLE\"><a href=\"question_pool.php\">".get_lang("QuestionPool")."</a> | ",
			"<img src=\"../img/jqz.jpg\" alt=\"HotPotatoes\" valign=\"ABSMIDDLE\">&nbsp;<a href=\"hotpotatoes.php\">".get_lang("ImportHotPotatoesQuiz")."</a>",
			"</td>",
			"<td width=\"50%\" align=\"right\">";
	}
	else
	{
		//error_log('!is_allowedToEdit or origin == learnpath ('.$origin.')',0);
		echo "<td align=\"right\">";
	}

	//get HotPotatoes files (active and inactive)
	$res = api_sql_query ("SELECT *
					FROM $TBL_DOCUMENT
					WHERE
					path LIKE '".$uploadPath."/%/%'",__FILE__,__LINE__);
	$nbrTests = Database::num_rows($res);
	$res = api_sql_query ("SELECT *
					FROM $TBL_DOCUMENT d, $TBL_ITEM_PROPERTY ip
					WHERE  d.id = ip.ref
					AND ip.tool = '".TOOL_DOCUMENT."'
					AND d.path LIKE '".$uploadPath."/%/%'
					AND ip.visibility='1'", __FILE__,__LINE__);
	$nbrActiveTests = Database::num_rows($res);
	//error_log('nbrActiveTests = '.$nbrActiveTests,0);


	if($is_allowed[EDIT_RIGHT])
	{//if user is allowed to edit, also show hidden HP tests
		$nbrHpTests = $nbrTests;
	}else
	{
		$nbrHpTests = $nbrActiveTests;
	}
	$nbrNextTests = $nbrHpTests-(($page*$limitExPage));


	//show pages navigation link for previous page
	if($page)
	{
		echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&page=".($page-1)."\">&lt;&lt; ",get_lang("PreviousPage")."</a> | ";
	}
	elseif($nbrExercises+$nbrNextTests > $limitExPage)
	{
		echo "&lt;&lt; ",get_lang("PreviousPage")." | ";
	}

	//show pages navigation link for previous page
	if($nbrExercises+$nbrNextTests > $limitExPage)
	{
		echo "<a href=\"".$_SERVER['PHP_SELF']."?".api_get_cidreq()."&page=".($page+1)."\">&gt;&gt; ",get_lang("NextPage")."</a>";

	}
	elseif($page)
	{
		echo get_lang("NextPage") . " &gt;&gt;";
	}

	echo "</td>",
			"</tr>",
			"</table>";

	echo "<table border=\"0\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" width=\"100%\">";

	// shows the title bar only for the administrator
	if (($is_allowed[EDIT_RIGHT]) and ($origin != 'learnpath'))
	{
		echo "<tr bgcolor=\"#e6e6e6\">",
			"<td align=\"center\">",get_lang("ExerciseName")."</td>",
			"<td align=\"center\">",get_lang("Modify")."</td>";

		if ($is_allowed[DELETE_RIGHT]) echo "<td align=\"center\">",get_lang("Delete")."</td>";
		echo "<td align=\"center\">",get_lang("Activate")." / ",get_lang("Deactivate")."</td>",
			"</tr>";
	}

	// show message if no HP test to show
	if(!($nbrExercises+$nbrHpTests) )
	{
	?>
		<tr>
		  <td <?php if($is_allowed[EDIT_RIGHT]) echo 'colspan="4"'; ?>><?php echo get_lang("NoEx"); ?></td>
		</tr>
	<?php
	}

	$i=1;

	// while list exercises

	if ($origin != 'learnpath') {

		while($row=mysql_fetch_array($result))
		{
			//error_log($row[0],0);
			echo "<tr>\n";
			// prof only
			if($is_allowed[EDIT_RIGHT])
			{
				?>
				<td width="60%">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="20" valign="top" align="right"><?php echo ($i+($page*$limitExPage)).'.'; ?></td>
							<td width="1">&nbsp;</td>
							<?php $row['title']=api_parse_tex($row['title']); ?>
							<td><a href="exercice_submit.php?<?php echo api_get_cidreq()."&origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id"; ?>&exerciseId=<?php echo $row['id']; ?>" <?php if(!$row['active']) echo 'class="invisible"'; ?>><?php echo $row['title']; ?></a></td>
						</tr>
					</table>
				</td>
				<td width="10%" align="center">
					<a href="admin.php?exerciseId=<?php echo $row[id]; ?>">
					<img src="../img/edit.gif" border="0" alt="<?php echo htmlentities(get_lang('Modify')); ?>" /></a>
				</td>
				<?php if ($is_allowed[DELETE_RIGHT])
				{
					?>
					<td width="10%" align="center">
						<a href="exercice.php?choice=delete&exerciseId=<?php echo $row[id]; ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('ConfirmYourChoice'))); ?>')) return false;">
						<img src="../img/delete.gif" border="0" alt="<?php echo htmlentities(get_lang('Delete')); ?>" /></a>
					</td>
					<?php
				}
				// if active
				if($row['active'])
				{
					?>

					<td width="20%" align="center">
					 <a href="exercice.php?choice=disable&page=<?php echo $page; ?>&exerciseId=<?php echo $row['id']; ?>">
					 <img src="../img/visible.gif" border="0" alt="<?php echo htmlentities(get_lang('Deactivate')); ?>" /></a>
					</td>
					<?php
				}
				// else if not active
				else
				{
					?>
					<td width="20%" align="center">
					 <a href="exercice.php?choice=enable&page=<?php echo $page; ?>&exerciseId=<?php echo $row['id']; ?>">
					 <img src="../img/invisible.gif" border="0" alt="<?php echo htmlentities(get_lang('Activate')); ?>" /></a>
					</td>
					<?php
				}
				echo "</tr>\n";
			}
			// student only
			else
			{
				?>
				<td width="100%">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
					<td width="20" valign="top" align="right"><?php echo ($i+($page*$limitExPage)).'.'; ?></td>
					<td width="1">&nbsp;</td>
					<?php $row['title']=api_parse_tex($row['title']);?>
					<td><a href="exercice_submit.php?<?php echo api_get_cidreq()."&origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id"; ?>&exerciseId=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
					</tr>
					</table>
				</td>
				</tr>
				<?php
			}

			// skips the last exercise, that is only used to know if we have or not to create a link "Next page"
			if($i == $limitExPage)
			{
				break;
			}

			$i++;
		}	// end while()

		$ind = $i;

		if (($from+$limitExPage-1)>$nbrexerc)
		{
			if($from>$nbrexerc)
			{
				$from = $from - $nbrexerc;
			  $to = $limitExPage;
			}
			else
			{
				$to = $limitExPage-($nbrexerc-$from);
				$from = 0;
			}
		}
		if($is_allowed[EDIT_RIGHT])
		{
			$sql = "SELECT d.path as path, d.comment as comment, ip.visibility as visibility
				FROM $TBL_DOCUMENT d, $TBL_ITEM_PROPERTY ip
							WHERE   d.id = ip.ref AND ip.tool = '".TOOL_DOCUMENT."' AND
							 (d.path LIKE '%htm%' OR d.path LIKE '%html%')
							AND   d.path  LIKE '".$uploadPath."/%/%' LIMIT $from,$to"; // only .htm or .html files listed
			$result = api_sql_query ($sql,__FILE__,__LINE__);
			//error_log($sql,0);
		}
		else
		{
			$sql = "SELECT d.path as path, d.comment as comment, ip.visibility as visibility
				FROM $TBL_DOCUMENT d, $TBL_ITEM_PROPERTY ip
								WHERE d.id = ip.ref AND ip.tool = '".TOOL_DOCUMENT."' AND
								 (d.path LIKE '%htm%' OR d.path LIKE '%html%')
								AND   d.path  LIKE '".$uploadPath."/%/%' AND ip.visibility='1' LIMIT $from,$to";
			$result = api_sql_query($sql, __FILE__, __LINE__); // only .htm or .html files listed
			//error_log($sql,0);
		}
		//error_log(mysql_num_rows($result),0);
		while($row = Database::fetch_array($result, 'ASSOC'))
		{
			//error_log('hop',0);
			$attribute['path'      ][] = $row['path'      ];
			$attribute['visibility'][] = $row['visibility'];
			$attribute['comment'   ][] = $row['comment'   ];
		}
		$nbrActiveTests = 0;
		if(is_array($attribute['path']))
		{
			while(list($key,$path) = each($attribute['path']))
			{
				list($a,$vis)=each($attribute['visibility']);
				if (strcmp($vis,"1")==0)
				{ $active=1;}
				else
				{ $active=0;}
				echo "<tr>\n";

				$title = GetQuizName($path,$documentPath);
				if ($title =='')
				{
					$title = GetFileName($path);
				}
				// prof only
				if($is_allowed[EDIT_RIGHT])
				{
					/************/
					?>
				 <td width="60%">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
						  <td width="40" align="right"><?php echo ($ind+($page*$limitExPage)).'.'; ?><img src="../img/jqz.jpg" alt="HotPotatoes"></td>
						  <td width="1">&nbsp;</td>
						  <td><a href="showinframes.php?file=<?php echo $path?>&cid=<?php echo $_course['official_code'];?>&uid=<?php echo $_uid;?>" <?php if(!$active) echo 'class="invisible"'; ?>><?php echo $title?></a></td>

							</tr>
						</table>
				  </td>

				  <td width="10%" align="center">
						<a href="adminhp.php?hotpotatoesName=<?php echo $path; ?>">
						<img src="../img/edit.gif" border="0" alt="<?php echo htmlentities(get_lang('Modify')); ?>" /></a>
					</td>

					<td width="10%" align="center"><a href="<?php echo $exercicePath; ?>?hpchoice=delete&file=<?php echo $path; ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('AreYouSureToDelete')." ".($ind+($page*$limitExPage).".".get_lang('Exercice')))); ?>')) return false;"><img src="../img/delete.gif" border="0" alt="<?php echo htmlentities(get_lang('Delete')); ?>"></a></td>

					<?php
					// if active
					if($active)
					{
						$nbrActiveTests = $nbrActiveTests + 1;
						?>

					  <td width="20%" align="center"><a href="<?php echo $exercicePath; ?>?hpchoice=disable&page=<?php echo $page; ?>&file=<?php echo $path; ?>"><img src="../img/visible.gif" border="0" alt="<?php echo htmlentities(get_lang('Deactivate')); ?>"></a></td>

						<?php
					}
					// else if not active
					else
					{
						?>
					  <td width="20%" align="center"><a href="<?php echo $exercicePath; ?>?hpchoice=enable&page=<?php echo $page; ?>&file=<?php echo $path; ?>"><img src="../img/invisible.gif" border="0" alt="<?php echo htmlentities(get_lang('Activate')); ?>"></a></td>
						<?php
					}
				/****************/
				}
				// student only
				else
				{
					if ($active==1)
					{
						$nbrActiveTests = $nbrActiveTests + 1;
						?>
						<tr>
							<td width="100%">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="40" align="right"><?php echo ($ind+($page*$limitExPage)).'.'; ?><img src="../img/jqz.jpg" alt="HotPotatoes"></td>
										<td width="1">&nbsp;</td>
										<td><a href="showinframes.php?<?php echo api_get_cidreq()."&file=".$path."&cid=".$_course['official_code']."&uid=".$_uid.'"'; if(!$active) echo 'class="invisible"'; ?>><?php echo $title?></a></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
					}
				}
				?>
				 </tr>
				<?php
				if($ind == $limitExPage)
				{
					break;
				}
				if($is_allowed[EDIT_RIGHT])
				{
					$ind++;
				}
				else
				{
					if ($active==1)
					{
						$ind++;
					}
				}
			}
		}
	} //end if ($origin != 'learnpath') {
	?>

	</table>

	<?php
}else{
	if($origin != 'learnpath'){
		echo '<a href="'.api_add_url_param($_SERVER['REQUEST_URI'],'show=test').'">&lt;&lt; '.get_lang('Back').'</a>';
	}
}// end if($show == 'test')

/*****************************************/
/* Exercise Results (uses tracking tool) */
/*****************************************/

// if tracking is enabled
if($is_trackingEnabled){
	?>

 <br><br>
 <h3><?php echo $is_allowed[EDIT_RIGHT]?get_lang('StudentResults'):get_lang('YourResults'); ?></h3>

	<?php
	if($show == 'result'){
		?>

		<table cellpadding="2" cellspacing="2" border="0" width="100%">
		 <tr bgcolor="#E6E6E6" align="center">
		  <?php if($is_allowed[EDIT_RIGHT]): ?>
			<td width="20%"><?php echo get_lang("User"); ?></td><?php endif; ?>
		  <td width="<?php if($is_allowed[EDIT_RIGHT]) echo '35'; else echo '55'; ?>%"><?php echo get_lang("Exercice"); ?></td>
		  <td width="30%"><?php echo get_lang("Date"); ?></td>
		  <td width="15%"><?php echo get_lang("Result"); ?></td>
		 </tr>

		<?php
		if($is_allowed[EDIT_RIGHT])
		{
			//get all results (ourself and the others) as an admin should see them
			//AND exe_user_id <> $_uid  clause has been removed
			$sql="SELECT CONCAT(`lastname`,' ',`firstname`),`ce`.`title`, `te`.`exe_result` ,
						`te`.`exe_weighting`, UNIX_TIMESTAMP(`te`.`exe_date`)
				  FROM $TBL_EXERCICES AS ce , `$TBL_TRACK_EXERCICES` AS te, $TBL_USER AS user
				  WHERE `te`.`exe_exo_id` = `ce`.`id` AND `user_id`=`te`.`exe_user_id` AND `te`.`exe_cours_id`='$_cid'
				  ORDER BY `te`.`exe_cours_id` ASC, `ce`.`title` ASC, `te`.`exe_date`ASC";

			$hpsql="SELECT CONCAT(tu.lastname,' ',tu.firstname), tth.exe_name,
						tth.exe_result , tth.exe_weighting, UNIX_TIMESTAMP(tth.exe_date)
					FROM `$TBL_TRACK_HOTPOTATOES` tth, $TBL_USER tu
					WHERE  tu.user_id=tth.exe_user_id AND tth.exe_cours_id = '".$_cid."'
					ORDER BY tth.exe_cours_id ASC, tth.exe_date ASC";

		}
		else
		{ // get only this user's results
			$sql="SELECT '',`ce`.`title`, `te`.`exe_result` , `te`.`exe_weighting`, UNIX_TIMESTAMP(`te`.`exe_date`)
				  FROM $TBL_EXERCICES AS ce , `$TBL_TRACK_EXERCICES` AS te
				  WHERE `te`.`exe_exo_id` = `ce`.`id` AND `te`.`exe_user_id`='$_uid' AND `te`.`exe_cours_id`='$_cid'
				  ORDER BY `te`.`exe_cours_id` ASC, `ce`.`title` ASC, `te`.`exe_date`ASC";

			$hpsql="SELECT '',exe_name, exe_result , exe_weighting, UNIX_TIMESTAMP(exe_date)
					FROM `$TBL_TRACK_HOTPOTATOES`
					WHERE exe_user_id = '$_uid' AND exe_cours_id = '".$_cid."'
					ORDER BY exe_cours_id ASC, exe_date ASC";

		}

		$results=getManyResultsXCol($sql,5);
		$hpresults=getManyResultsXCol($hpsql,5);

		$NoTestRes = 0;
		$NoHPTestRes = 0;

		if(is_array($results))
		{
			for($i = 0; $i < sizeof($results); $i++)
			{

		?>
		 <tr>
		  <?php if($is_allowed[EDIT_RIGHT]): ?>
			<td class="content"><?php echo $results[$i][0]; ?></td><?php endif; ?>
		  <td class="content"><?php echo $results[$i][1]; ?></td>
		  <td class="content" align="center"><?php echo strftime($dateTimeFormatLong,$results[$i][4]); ?></td>
		  <td class="content" align="center"><?php echo $results[$i][2]; ?> / <?php echo $results[$i][3]; ?></td>
		 </tr>

		<?php
			}
		}
		else
		{
				$NoTestRes = 1;
		}

		// The Result of Tests
		if(is_array($hpresults))
		{

			for($i = 0; $i < sizeof($hpresults); $i++)
			{
				$title = GetQuizName($hpresults[$i][1],$documentPath);
				if ($title =='')
				{
					$title = GetFileName($hpresults[$i][1]);
				}
		?>
		<tr>
		<?php if($is_allowed[EDIT_RIGHT]): ?>
			<td class="content"><?php echo $hpresults[$i][0]; ?></td><?php endif; ?>
		  <td class="content"><?php echo $title; ?></td>
		  <td class="content" align="center"><?php echo strftime($dateTimeFormatLong,$hpresults[$i][4]); ?></td>
		  <td class="content" align="center"><?php echo $hpresults[$i][2]; ?> / <?php echo $hpresults[$i][3]; ?></td>
		</tr>

		<?php
			}
		}
		else
		{
			$NoHPTestRes = 1;
		}



		if ($NoTestRes==1 && $NoHPTestRes==1)
		{
		?>

		 <tr>
		  <td colspan="3"><?php echo get_lang("NoResult"); ?></td>
		 </tr>

		<?php
		}

		?>

		</table>

		<?php
	}else{

		echo '<p><a href="'.api_add_url_param($_SERVER['REQUEST_URI'],'show=result').'">'.get_lang("Show").' &gt;&gt;</a></p>';

	}// end if($show == 'result')

}// end if tracking is enabled

if ($origin != 'learnpath') { //so we are not in learnpath tool
	Display::display_footer();
} else {
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $clarolineRepositoryWeb ?>css/default.css" />
	<?php
}
?>