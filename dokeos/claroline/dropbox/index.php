<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Jan Bols & Rene Haentjens (UGent)
	
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
 * Dropbox module for Dokeos
 * Main GUI
 * 
 * @author Jan Bols, original design and implementation
 * @author Rene Haentjens, mailing, feedback, folders, user-sortable tables
 * @author Roan Embrechts, virtual course support
 * @author Patrick Cool, config settings, tool introduction and refactoring
 * @package dokeos.dropbox
==============================================================================
*/

/*
* Boot the system
*/

require_once("dropbox_init.inc.php");

if (api_sql_query( "SELECT folder FROM " . dropbox_cnf("personTbl")) == FALSE)
	api_sql_query( "ALTER TABLE " . dropbox_cnf("personTbl") . " ADD COLUMN
					folder text default ''
  					") or die( dropbox_lang("installError")." (code 308)");

include_once(api_get_library_path().'/events.lib.inc.php');
event_access_tool(TOOL_DROPBOX);


/*
* This script can produce a standard Dropbox overview or a Mailing detail
*/

if (($mailing_id = get_url_param('mailing')))
	getUserOwningThisMailing($mailing_id, api_get_user_id(), '304');  // or die

$dropbox_person = new Dropbox_Person($mailing_id ? $mailing_id : api_get_user_id(), 
    $is_courseAdmin, $is_courseTutor);  // Mailing id = pseudo user id

$mailingInUrl = $mailing_id ? '&mailing=' . urlencode($mailing_id) : '';

$sorting_options = array(); $paging_options = array();
if (!get_url_param('column') && !get_url_param('direction'))
    $sorting_options = array('column'=>'4', 'direction'=>'3');

/*
* Javascript and htmlHeaders (currently untouched from 1.6)
*/

$javascript = "<script type=\"text/javascript\">
	/* <![CDATA[ */
	function confirmsend ()
	{
		if (confirm(\"".dropbox_lang("mailingConfirmSend", "noDLTT")."\")){
			return true;
		} else {
			return false;
		}
		return true;
	}

	function confirmation (name)
	{
		if (confirm(\"".dropbox_lang("confirmDelete", "noDLTT")." : \"+ name )){
			return true;
		} else {
			return false;
		}
		return true;
	}

	function checkForm (frm)
	{
		if (frm.elements['recipients[]'].selectedIndex < 0){
			alert(\"".dropbox_lang("noUserSelected", "noDLTT")."\");
			return false;
		} else if (frm.file.value == '') {
			alert(\"".dropbox_lang("noFileSpecified", "noDLTT")."\");
			return false;
		} else {
			return true;
		}
	}
	";

if (dropbox_cnf("allowOverwrite"))
{
	$javascript .= "
		var sentArray = new Array(''";
		//sentArray keeps list of all files still available in the sent files list
		//of the user.
		//This is used to show or hide the overwrite file-radio button of the upload form
	foreach ($dropbox_person->sentWork as $w)
		$javascript .= ", '".$w->title."'";
	$javascript .=");

		function checkfile(str)
		{

			ind = str.lastIndexOf('/'); //unix separator
			if (ind == -1) ind = str.lastIndexOf('\\\');	//windows separator
			filename = str.substring(ind+1, str.length);

			found = 0;
			for (i=0; i<sentArray.length; i++) {
				if (sentArray[i] == filename) found=1;
			}

			//always start with unchecked box
			el = getElement('cb_overwrite');
			el.checked = false;

			//show/hide checkbox
			if (found == 1) {
				displayEl('overwrite');
			} else {
				undisplayEl('overwrite');
			}
		}

		function getElement(id)
		{
			return document.getElementById ? document.getElementById(id) :
			document.all ? document.all(id) : null;
		}

		function displayEl(id)
		{
			var el = getElement(id);
			if (el && el.style) el.style.display = '';
		}

		function undisplayEl(id)
		{
			var el = getElement(id);
			if (el && el.style) el.style.display = 'none';
		}";
}

$javascript .="
	/* ]]> */
	</script>";

$htmlHeadXtra[] = $javascript;
// api_session_register('javascript');

$htmlHeadXtra[] = '<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="-1">';

if ($origin != 'learnpath')
{
    Display::display_header($nameTools,"Dropbox");
}
else
{
	echo '<link rel="stylesheet" type="text/css" href="', 
	    api_get_path(WEB_CODE_PATH), 'css/default.css">', $javascript;
}

api_display_tool_title($nameTools);


/*
* Some functionality is only available in the standard Dropbox overview
*/

if ($mailing_id)
{
    echo '<h3>', htmlspecialchars(getUserNameFromId($mailing_id)), '</h3>';
}
else
{
    //Introduction section
    Display::display_introduction_section(TOOL_ANNOUNCEMENT, $is_allowed);
    
    // Return from folder view with updated filing info
    
    if (isset($_POST["foldersubmit"]) && isset($_POST["newfolder"]))
    {
		$newfolder = get_magic_quotes_gpc() ? 
        	stripslashes($_POST["newfolder"]) : $_POST["newfolder"];
        
        foreach($dropbox_person->receivedWork as $id => $w) {
			if ($w->folder != $newfolder && isset($_POST["v" . $id]) && 
			        isset($_POST["f" . $id]))
                    $dropbox_person->fileReceivedWork($id, $newfolder);
		}
		
		foreach($dropbox_person->sentWork as $id => $w)
			if ($w->folder != $newfolder && isset($_POST["v" . $id]) && 
			        isset($_POST["f" . $id]))
                    $dropbox_person->fileSentWork($id, $newfolder);
		
		$dropbox_person->recalcFolders();
    }
    
    dbv($dropbox_person->folders, 9999);
    
    
    // Return from dropbox_submit with new or updated feedback
    
    if (isset($_POST["feedbackid"]) && isset($_POST["feedbacktext"]))
    {
    	$dropbox_person->updateFeedback($_POST["feedbackid"], get_magic_quotes_gpc() ? 
    	stripslashes($_POST["feedbacktext"]) : $_POST["feedbacktext"]);
    }
    
    // Received work filter
    
    if (($dropbox_user_filter = get_url_param('dropbox_user_filter')))
    	$dropbox_person->filter_received_work('uploader_id',$dropbox_user_filter);
    
    // Received work header and list
    
    $column_header =   array(); $dropbox_data =   array();
    
    $column_header[] = array(dropbox_lang('receivedTitle'), TRUE, '');
    $column_header[] = array(dropbox_lang('authors'), TRUE, '');
    $column_header[] = array(dropbox_lang('description'), TRUE, '');
    $column_header[] = array(dropbox_lang('size'), TRUE, '');
    $column_header[] = array(nbs(dropbox_lang('lastResent')), TRUE, '');
    $column_header[] = array(nbs(' '), FALSE, '', 'nowrap style="text-align: right"');
    
    foreach ($dropbox_person->receivedWork as $id => $w) if (!$w->folder) 
    {
        $ddr = array(); $w_title = htmlspecialchars($w->title);
        $w_id = urlencode($id);
        
        $ddr[] = 
            dropbox_link('dropbox_download', URL_OR.'&id='.$w_id, $w_title);
        $ddr[] = nbs($w->author);
        $ddr[] = htmlspecialchars($w->description);
        $ddr[] = $w->filesize;
        
        $rddr = nbs($w->last_upload_date);
        $ddr[] = $w->uploaderName == $w->author ? $rddr : 
            $rddr . ' (' . nbs($w->uploaderName . ')');
        
    	$ddr[] = 
			dbs_link('comment', dbla('giveFeedback') . ($w->feedback ? 
			    ' (' . $w->feedback_date . ')' : ''), '&editFeedback='.$w_id) . 
    	    dbs_link('delete', get_lang('Delete'), '&deleteReceived='.$w_id, 
    	        "return confirmation('" . addslashes($w_title) . "');");
        
        $dropbox_data[] = $ddr;
    }
    
	$complete_user_list_for_dropbox = CourseManager::get_real_and_linked_user_list($course_id);
	
	foreach ($complete_user_list_for_dropbox as $k => $e) 
	    $complete_user_list_for_dropbox[$k] = $e + 
	        array('lastcommafirst' => $e['lastname'] . ', ' . $e['firstname']);

	$complete_user_list_for_dropbox = TableSort::sort_table($complete_user_list_for_dropbox, 'lastcommafirst');

	$stuff_2 = ' - ' . dropbox_lang('sentBy') . ' ' . 
        '<select name="dropbox_user_filter" onchange="javascript: this.form.submit()">' . "\n" . 
        '<option value="-1">' . get_lang('All') . '</option>' . "\n";
    foreach ($complete_user_list_for_dropbox as $current_user)
    	$stuff_2 .= '<option value="' . $current_user['user_id'] . '"' . 
    	    ($dropbox_user_filter == $current_user['user_id'] ? ' selected="selected"' : '') . 
    	    '>' . $current_user['lastcommafirst'] . '</option>' . "\n";
    $stuff_2 .= '</select><noscript><input type="submit" value="OK"/></noscript>' . "\n"; 

    dfh(dropbox_lang('receivedTitle'), '&deleteReceived=all' . 
        ($dropbox_user_filter ? '&dropbox_user_filter=' . $dropbox_user_filter : ''), 
        '<form name="formReceived" method="get" action="index.php' . URL_ORUN . '">' . "\n", 
        $stuff_2, '</form>' . "\n");
    
    Display::display_sortable_table($column_header, $dropbox_data, $sorting_options, $paging_options);
    echo '<br><br>';
}


/*
* Sent work is common to standard Dropbox overview and Mailing detail
*/

$column_header =   array(); $dropbox_data =   array();

$column_header[] = array(dropbox_lang('sentTitle'), TRUE, '');
$column_header[] = array(dropbox_lang('authors'), TRUE, '');
$column_header[] = array(dropbox_lang('description'), TRUE, '');
$column_header[] = array(dropbox_lang('size'), TRUE, '');
$column_header[] = array(nbs(dropbox_lang('lastResent')), TRUE, '');
$column_header[] = array(nbs(dropbox_lang('sentTo')), TRUE, '');
$column_header[] = array(nbs(' '), FALSE, '', 'nowrap style="text-align: right"');

foreach ($dropbox_person->sentWork as $w) if (!$w->folder) 
{
    $ddr = array(); $w_title = htmlspecialchars($w->title);
    $w_id = urlencode($w->id);
    
    $first_recip = count($w->recipients) ? $w->recipients[0]['id'] : 0;
    $is_mailing = $first_recip > dropbox_cnf("mailingIdBase");
    
    $ddr[] = $is_mailing ? 
        dropbox_link('index', URL_OR.'&mailing='.urlencode($first_recip), 
            '<i>' . $w_title . '</i>') : 
        dropbox_link('dropbox_download', URL_OR.$mailingInUrl . '&id='.$w_id, 
            $w_title);
    $ddr[] = nbs($w->author);
    $ddr[] = htmlspecialchars($w->description);
    $ddr[] = $w->filesize;
    $ddr[] = nbs($w->last_upload_date); $rddr = '';
    
    foreach ($w->recipients as $r) $rddr .= nbs($r['name']) . ', ';
    $ddr[] = $w->upload_date == $w->last_upload_date ? 
        substr($rddr, 0, strlen($rddr)-2) : 
        $rddr . dropbox_lang('sentOn') . nbs(' ' . $w->upload_date);
    
    $rddr = '';
    if ($is_mailing)
    {
        $rddr = dbs_link('checkzip', dbla('mailingExamine'), 
            '&mailingIndex='.$w_id);
        if ($w->filesize != 0) $rddr .= dbs_link('sendzip', dbla('mailingSend'), 
            '&mailingSend=yes&mailingIndex='.$w_id, 'return confirmsend();');
    }
    
    $lastfeedbackdate = ''; $lastfeedbackfrom = '';
    foreach ($w ->recipients as $r) if (($fb = $r["feedback"]))
        if ($r["feedback_date"] > $lastfeedbackdate)
        {
            $lastfeedbackdate = $r["feedback_date"]; $lastfeedbackfrom = $r["name"];
        }
    
    if ($lastfeedbackdate)
    {
        $rddr .= dbs_link('comment', dbla('showFeedback') . 
            ' (' . $lastfeedbackdate . ': ' . $lastfeedbackfrom . ')', 
            '&showFeedback='.$w_id . $mailingInUrl);
    }
    
	$ddr[] = $rddr . dbs_link('delete', get_lang('Delete'), 
	        '&deleteSent='.$w_id . $mailingInUrl, 
	        "return confirmation('" . addslashes($w_title) . "');");
    
    $dropbox_data[] = $ddr;
}

dfh(dropbox_lang('sentTitle'), '&deleteSent=all' . $mailingInUrl, '<form>', '', '</form>');

Display::display_sortable_table($column_header, $dropbox_data, $sorting_options, 
    $paging_options, $mailing_id ? array('mailing' => $mailing_id) : NULL);


/*
* Upload is only available in standard Dropbox overview (untouched from 1.6)
*/

if (!$mailing_id)
{
	?>
	<br><br>
	<form method="post" action="dropbox_submit.php<?php echo URL_ORUN; ?>" enctype="multipart/form-data" onsubmit="return checkForm(this)">
	<table border="0">
		<tr>
			<td  align="right">
				<?php echo dropbox_lang("uploadFile")?>:
			</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value='<?php echo dropbox_cnf("maxFilesize")?>' />
				<input type="file" name="file" size="20" />
			</td>
		</tr>
	<?php
	/* RH 2006/01/19: removed from input type="file" above:
	   <?php if (dropbox_cnf("allowOverwrite")) echo 'onChange="checkfile(this.value)"'; ?>
	   and from tr id="overwrite" below: style="display: none" */
	if (dropbox_cnf("allowOverwrite"))
	{
		?>
		<tr id="overwrite">
			<td valign="top"  align="right">
			</td>
			<td>
				<input type="checkbox" name="cb_overwrite" id="cb_overwrite" value="true" /><?php echo dropbox_lang("overwriteFile")?>
			</td>
		</tr>
		<?php
	}
	?>
		<tr>
			<td valign="top"  align="left">
				<?php echo dropbox_lang("authors")?>:
			</td>
			<td>
				<input type="text" name="authors" value="<?php echo getUserNameFromId( api_get_user_id())?>" size="32" />
			</td>
		</tr>
		<tr>
			<td valign="top"  align="left">
				<?php echo dropbox_lang("description")?>:
			</td>
			<td>
				<textarea name="description" cols="24" rows="2"></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top"  align="left">
				<?php echo dropbox_lang("sendTo")?>:
			</td>
			<td valign="top"  align="left">
				<select name="recipients[]" size="
	<?php
		if ( $dropbox_person -> isCourseTutor || $dropbox_person -> isCourseAdmin)
		{
			echo 5;
		}
		else
		{
			echo 3;
		}
	?>" multiple style="width: 220px;">
	<?php
	
	//list of all users in this course and all virtual courses combined with it
	$complete_user_list_for_dropbox = CourseManager::get_real_and_linked_user_list($course_id);
	
	foreach ($complete_user_list_for_dropbox as $k => $e) 
	    $complete_user_list_for_dropbox[$k] = $e + 
	        array('lastcommafirst' => $e['lastname'] . ', ' . $e['firstname']);

	$complete_user_list_for_dropbox = TableSort::sort_table($complete_user_list_for_dropbox, 'lastcommafirst');
	
	/*
		Create the options inside the select box:
		List all selected users their user id as value and a name string as display
	*/
	foreach ($complete_user_list_for_dropbox as $current_user)
	{
		if ( ($dropbox_person -> isCourseTutor 
		|| $dropbox_person -> isCourseAdmin
		|| dropbox_cnf("allowStudentToStudent")
		|| $current_user['status']!=5				// always allow teachers
		|| $current_user['tutor_id']==1				// always allow tutors	
		) && $current_user['user_id'] != api_get_user_id() ) 	// don't include yourself
		{
			$full_name = $current_user['lastcommafirst'];
			echo '<option value="user_' . $current_user['user_id'] . '">' . $full_name . '</option>';
		}
	}
	
	/*
	* Show groups
	*/
    if ( ($dropbox_person -> isCourseTutor || $dropbox_person -> isCourseAdmin) 
    && dropbox_cnf("allowGroup") || dropbox_cnf("allowStudentToStudent"))  
    {
		$complete_group_list_for_dropbox = GroupManager::get_group_list(null,api_get_course_id());
		
		if (count($complete_group_list_for_dropbox) > 0) 
		{
			foreach ($complete_group_list_for_dropbox as $current_group) 
			{
				if ($current_group['number_of_members'] > 0) 
				{
					echo '<option value="group_'.$current_group['id'].'">G: '.$current_group['name'].' - '.$current_group['number_of_members'].' '.$langUsers.'</option>';
				}
			}
		}
    }

    if ( ($dropbox_person -> isCourseTutor || $dropbox_person -> isCourseAdmin) && dropbox_cnf("allowMailing"))
	{
			echo '<option value="mailing">'.dropbox_lang("mailingInSelect").'</option>';
	}

    if ( dropbox_cnf("allowJustUpload"))
    {
	  echo '<option value="upload">'.dropbox_lang("justUploadInSelect").'</option>';
    }

	echo "</select>",
		"</td></tr>", 
		"<tr><td></td>",
		"<td><input type=\"Submit\" name=\"submitWork\" value=\"".dropbox_lang("ok", "noDLTT")."\" />",
		"</td></tr>",
		"</table>",
		"</form>";
}

if ($mailing_id) echo dropbox_link('index', URL_OR, dropbox_lang('mailingBackToDropbox'));

if ($origin != 'learnpath')
{
	//we are not in the learning path tool
	Display::display_footer();
}
?>
