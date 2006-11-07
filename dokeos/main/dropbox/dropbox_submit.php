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
 * Handles actions submitted from index.php and dropbox_folder.php
 * 
 * @author Jan Bols, original design and implementation
 * @author Rene Haentjens, mailing, feedback, folders, user-sortable tables
 * @author Roan Embrechts, virtual course support
 * @author Patrick Cool, config settings, tool introduction and refactoring
 * @package dokeos.dropbox
==============================================================================
*/

require_once("dropbox_init.inc.php");


function echo_go_back()
{
    echo dropbox_link('index', URL_OR, dropbox_lang("mailingBackToDropbox")), '<br>';
}

/*
 * ================
 * PREVENT RESUBMIT
 * ================
 * Note: only works with GET, annoying behaviours with POST...
 */

($dropbox_unid = get_url_param('dropbox_unid', '^[0-9a-f]+$'))
    or die(dropbox_lang("badFormData")." (code 400)");

if (isset($_SESSION["dropbox_uniqueid"]) && $dropbox_unid == $_SESSION["dropbox_uniqueid"])
{
	//resubmit : go to index.php
    header("Location: http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? "s" : "") . 
        "://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
	exit;
}
$dropbox_uniqueid = $dropbox_unid; api_session_register("dropbox_uniqueid");


/**
 * ==================================================
 * FORM SUBMIT: VALIDATE POSTED DATA, UPLOAD NEW FILE
 * ==================================================
 * Note: only output in case of error, otherwise redirect to calling script
 */

if ( isset( $_POST["submitWork"]))
{
    require_once(api_get_library_path() . "/fileUpload.lib.php");

    $error = FALSE; $errormsg = '';

    /**
     * ---------------------------------------
     * FORM SUBMIT : VALIDATE POSTED DATA
     * ---------------------------------------
     */
    if ( !isset( $_POST['authors']) || !isset( $_POST['description']))
    {
        $error = TRUE; $errormsg = dropbox_lang("badFormData");
    }
	elseif ( !isset( $_POST['recipients']) || count( $_POST['recipients']) <= 0)
    {
        $error = TRUE; $errormsg = dropbox_lang("noUserSelected");
    }
    else
    {
        $thisIsAMailing = FALSE; $thisIsJustUpload = FALSE;
        
	    foreach( $_POST['recipients'] as $rec)
        {
            if ( $rec == 'mailing')
            {
	            $thisIsAMailing = TRUE;
            }
            elseif ( $rec == 'upload')
            {
	            $thisIsJustUpload = TRUE;
            }
	        elseif (strpos($rec, 'user_') === 0 && 
	            !CourseManager::is_user_subscribed_in_real_or_linked_course(substr($rec, strlen('user_') ), $course_id ))
	        {
	        	die( dropbox_lang("badFormData")." (code 401)");
	        }
	        elseif (strpos($rec, 'group_') !== 0 && strpos($rec, 'user_') !== 0)
	        {
	        	die( dropbox_lang("badFormData")." (code 402)");
	        }
        }

        if ( $thisIsAMailing && ( count($_POST['recipients']) != 1))
        {
            $error = TRUE; $errormsg = dropbox_lang("mailingSelectNoOther");
        }
        elseif ( $thisIsJustUpload && ( count($_POST['recipients']) != 1))
        {
            $error = TRUE; $errormsg = dropbox_lang("mailingJustUploadNoOther");
        }
        elseif ( empty( $_FILES['file']['name']))
        {
            $error = TRUE; $errormsg = dropbox_lang("noFileSpecified");
        }
    }

	//check if $_POST['cb_overwrite'] is true or false
	$dropbox_overwrite = false;
	if ( isset($_POST['cb_overwrite']) && $_POST['cb_overwrite']==true) $dropbox_overwrite = true;

    /**
     * ---------------------------------
     * FORM SUBMIT : UPLOAD NEW FILE
     * ---------------------------------
     */
    if ( !$error)
    {
        $dropbox_filename =     $_FILES['file']['name'];
        $dropbox_filesize =     $_FILES['file']['size'];
        $dropbox_filetype =     $_FILES['file']['type'];
        $dropbox_filetmpname =  $_FILES['file']['tmp_name'];

        if ( $dropbox_filesize <= 0 || $dropbox_filesize > dropbox_cnf("maxFilesize"))
        {
            $error = TRUE; $errormsg = dropbox_lang("tooBig");
        }
        elseif ( !is_uploaded_file( $dropbox_filetmpname)) // check user fraud : no clean error msg.
        {
            die ( dropbox_lang("badFormData")." (code 403)");
        }

        if ( !$error)
        { 
            // Try to add an extension to the file if it hasn't got one
            $dropbox_filename = add_ext_on_mime( $dropbox_filename,$dropbox_filetype); 
            // Replace dangerous characters
            $dropbox_filename = replace_dangerous_char( $dropbox_filename); 
            // Transform any .php file in .phps fo security
            $dropbox_filename = php2phps ( $dropbox_filename); 
			
            // set title
            $dropbox_title = $dropbox_filename;
			
            // set author
            if ( $_POST['authors'] == '')
            {
                $_POST['authors'] = getUserNameFromId( api_get_user_id());
            } 
			
			if ( $dropbox_overwrite)
			{
				$dropbox_person = new Dropbox_Person( api_get_user_id(), $is_courseAdmin, $is_courseTutor);
				
				foreach($dropbox_person->sentWork as $w)
				{
					if ($w->title == $dropbox_filename)
					{
					    if ((count($w->recipients) == 1 && $w->recipients[0]['id'] > dropbox_cnf("mailingIdBase")) xor $thisIsAMailing)
					    {
							$error = TRUE; $errormsg = dropbox_lang("mailingNonMailingError");
						}
						if ((count($w->recipients) == 0) xor $thisIsJustUpload)
						{
							$error = TRUE; $errormsg = dropbox_lang("mailingJustUploadNoOther");
						}
						$dropbox_filename = $w->filename;
						break;
					}
				}
			}
			else  // rename file to login_filename_uniqueId format
			{
				$dropbox_filename = getLoginFromId( api_get_user_id()) . "_" . $dropbox_filename . "_".uniqid(''); 
			}
                        
			if ( ( ! is_dir( dropbox_cnf("sysPath"))))
            {
				//The dropbox subdir doesn't exist yet so make it and create the .htaccess file
                mkdir( dropbox_cnf("sysPath"), 0700) or die ( dropbox_lang("errorCreatingDir")." (code 404)");
				$fp = fopen( dropbox_cnf("sysPath")."/.htaccess", "w") or die (dropbox_lang("errorCreatingDir")." (code 405)");
				fwrite($fp, "AuthName AllowLocalAccess
                             AuthType Basic

                             order deny,allow
                             deny from all

                             php_flag zlib.output_compression off") or die (dropbox_lang("errorCreatingDir")." (code 406)");
            }

			if ( $error) {}
            elseif ( $thisIsAMailing)  // $newWorkRecipients is integer - see class
			{
			    if ( preg_match( dropbox_cnf("mailingZipRegexp"), $dropbox_title))
				{
		            $newWorkRecipients = dropbox_cnf("mailingIdBase");
				}
				else
				{
			        $error = TRUE; $errormsg = $dropbox_title . ": " . dropbox_lang("mailingWrongZipfile");
				}
			}
			elseif ( $thisIsJustUpload)  // $newWorkRecipients is empty array
			{
	            $newWorkRecipients = array();
        	}
			else
			{
				$newWorkRecipients = array();
	            foreach ($_POST["recipients"] as $rec)
	            {
	            	if (strpos($rec, 'user_') === 0) {
	            		$newWorkRecipients[] = substr($rec, strlen('user_') );
	            	} 
	            	elseif (strpos($rec, 'group_') === 0 ) 
	            	{
	            		$userList = GroupManager::get_subscribed_users(substr($rec, strlen('group_') ));
	            		foreach ($userList as $usr) 
	            		{
	            			if (! in_array($usr['user_id'], $newWorkRecipients) && $usr['user_id'] != api_get_user_id())
	            			{
	            				$newWorkRecipients[] = $usr['user_id'];
	            			}
	            		}
	            	}
	            }
        	}
        	
			//After uploading the file, create the db entries
			
        	if ( !$error)
        	{
	            @move_uploaded_file( $dropbox_filetmpname, dropbox_cnf("sysPath") . '/' . $dropbox_filename) 
	            	or die( dropbox_lang("uploadError")." (code 407)");
	            new Dropbox_SentWork( api_get_user_id(), $dropbox_title, $_POST['description'], strip_tags($_POST['authors']), $dropbox_filename, $dropbox_filesize, $newWorkRecipients);
        	}
        }
    } //end if(!$error)
	
	
    /**
     * -------------------------
     * SUBMIT FORM RESULTMESSAGE
     * -------------------------
     */
    if ( !$error)
    {
        header("Location: http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? "s" : "") . 
            "://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
    	exit;  // instead of header + echo dropbox_lang("docAdd"), '<br><br>'; echo_go_back();
    }

    if ($origin != 'learnpath')
    {
        Display::display_header($nameTools,"Dropbox");
    }
    else
    {
    	echo '<link rel="stylesheet" type="text/css" href="', 
    	    api_get_path(WEB_CODE_PATH), 'css/default.css">';
    }
    
    api_display_tool_title($nameTools);

    echo '<b><font color="#FF0000">', $errormsg, '</font></b><br><br>';
	echo_go_back();

	if ($origin != 'learnpath')
	{
	   Display::display_footer();
	}

    exit;
} // end if ( isset( $_POST["submitWork"]))


/**
 * =======================
 * EXAMINE OR SEND MAILING
 * =======================
 */
 
if (($id = get_url_param('mailingIndex')))  // examine or send
{
    if ($origin != 'learnpath')
    {
        Display::display_header($nameTools,"Dropbox");
    }
    else
    {
    	echo '<link rel="stylesheet" type="text/css" href="', 
    	    api_get_path(WEB_CODE_PATH), 'css/default.css">';
    }
    
    api_display_tool_title($nameTools);

    $dropbox_person = new Dropbox_Person( api_get_user_id(), $is_courseAdmin, $is_courseTutor);
    
    $user_table =  Database::get_main_table(MAIN_USER_TABLE);
    $course_user = Database::get_main_table(MAIN_COURSE_USER_TABLE);
    
	if (!isset($dropbox_person->sentWork[$id])) die(dropbox_lang("generalError")." (code 4xx)");
	$mailing_item = $dropbox_person->sentWork[$id];
	
    $mailing_title = $mailing_item->title;
    $mailing_file = dropbox_cnf("sysPath") . '/' . $mailing_item->filename;
    $errormsg = '<b>' . $mailing_item->recipients[0]['name'] . ' (' . 
        dropbox_link('dropbox_download', URL_OR.'&id='.urlencode($mailing_item->id), 
            htmlspecialchars($mailing_title)) . '):</b><br><br>';
    
    if (preg_match( dropbox_cnf("mailingZipRegexp"), $mailing_title, $nameParts))
	{
		$var = strtoupper($nameParts[2]);  // the variable part of the name
		$sel = "SELECT u.user_id, u.lastname, u.firstname, cu.status 
				FROM $user_table u 
				LEFT JOIN $course_user cu 
				ON cu.user_id = u.user_id AND cu.course_code = '".$_course['sysCode']."'";
		$sel .= " WHERE u.".dropbox_cnf("mailingWhere".$var)." = '";
		
		function getUser($thisRecip)
		{
			// string result = error message, array result = [user_id, lastname, firstname]

			global $var, $sel;
			$students = array();

			$result = api_sql_query($sel . $thisRecip . "'",__FILE__,__LINE__);
			while ( ($res = mysql_fetch_array($result))) {$students[] = $res;}
			mysql_free_result($result);

			if (count($students) == 1)
			{
				return($students[0]);
			}
			elseif (count($students) > 1)
			{
				return ' <'.dropbox_lang('mailingFileRecipDup', 'noDLTT')." $var= $thisRecip>";
			}
			else
			{
				return ' <'.dropbox_lang('mailingFileRecipNotFound', 'noDLTT')." $var= $thisRecip>";
			}
		}
        
		$preFix = $nameParts[1]; $postFix = $nameParts[3];
		$preLen = strlen($preFix); $postLen = strlen($postFix);
		
		function findRecipient($thisFile)
		{
			// string result = error message, array result = [user_id, lastname, firstname, status]
			
			global $nameParts, $preFix, $preLen, $postFix, $postLen;
			
			$matches = array();
            if ( preg_match(dropbox_cnf("mailingFileRegexp"), $thisFile, $matches))
            {
	            $thisName = $matches[1];
	            if ( substr($thisName, 0, $preLen) == $preFix)
	            {
		            if ( $postLen == 0 || substr($thisName, -$postLen) == $postFix)
		            {
			            $thisRecip = substr($thisName, $preLen, strlen($thisName) - $preLen - $postLen);
			            if ( $thisRecip) return getUser($thisRecip);
			            return ' <'.dropbox_lang("mailingFileNoRecip", "noDLTT").'>';
		            }
		            else
		            {
			            return ' <'.dropbox_lang("mailingFileNoPostfix", "noDLTT").' '.$postFix.'>';
		            }
	            }
	            else
	            {
		            return ' <'.dropbox_lang("mailingFileNoPrefix", "noDLTT").' '.$preFix.'>';
	            }
            }
            else
            {
	            return ' <'.dropbox_lang("mailingFileFunny", "noDLTT").'>';
            }
        }
        
	    if (file_exists(api_get_include_path() . "/pclzip/pclzip.lib.php")) {
	        require(api_get_include_path() . "/pclzip/pclzip.lib.php");
	    } else {
	        require(api_get_library_path() . "/pclzip/pclzip.lib.php");
		}
		
		$zipFile = new pclZip($mailing_file);  $goodFiles  = array();
		$zipContent = $zipFile->listContent(); $ucaseFiles = array();
		
		if ( $zipContent)
		{
			foreach( $zipFile->listContent() as $thisContent)
			{
	            $thisFile = substr(strrchr('/' . $thisContent['filename'], '/'), 1);
	            $thisFileUcase = strtoupper($thisFile);
				if ( preg_match("~.(php.*|phtml)$~i", $thisFile) )
				{
		            $error = TRUE; $errormsg .= $thisFile . ': ' . dropbox_lang("mailingZipPhp");
					break;
				}
				elseif ( !$thisContent['folder'])
				{
		            if ( $ucaseFiles[$thisFileUcase])
		            {
			            $error = TRUE; $errormsg .= $thisFile . ': ' . dropbox_lang("mailingZipDups");
						break;
		            }
		            else
		            {
			            $goodFiles[$thisFile] = findRecipient($thisFile);
			            $ucaseFiles[$thisFileUcase] = "yep";
		            }
				}
					
			}
		}
		else
		{
            $error = TRUE; $errormsg .= dropbox_lang("mailingZipEmptyOrCorrupt");
        }
		
		if ( !$error)
		{
			$students = array();  // collect all recipients in this course
			
			foreach( $goodFiles as $thisFile => $thisRecip)
			{
				$errormsg .= htmlspecialchars($thisFile) . ': ';
	            if ( is_string($thisRecip))  // see findRecipient
	            {
					$errormsg .= '<font color="#FF0000">'
						. htmlspecialchars($thisRecip) . '</font><br>';
	            }
	            else
	            {
					if (get_url_param('mailingSend', '^yes$'))
					{
			            $errormsg .= dropbox_lang("mailingFileSentTo") . ' ';
		            }
		            else
		            {
						$errormsg .= dropbox_lang("mailingFileIsFor") . ' ';
		            }
					$errormsg .= htmlspecialchars($thisRecip[1].' '.$thisRecip[2]);
					
					if ( is_null($thisRecip[3]))
					{
						$errormsg .= ' ' . dropbox_lang("mailingFileNotRegistered");
					}
					else
					{
						$students[] = $thisRecip[0];
					}
					$errormsg .= '<br>';
					
	            }
			}
			
			// find student course members not among the recipients
			
			$sql = "SELECT u.lastname, u.firstname 
					FROM $course_user cu 
				    LEFT JOIN $user_table u 
					ON cu.user_id = u.user_id AND cu.course_code = '".$_course['sysCode']."'
					WHERE cu.status = 5 
					AND u.user_id NOT IN ('" . implode("', '" , $students) . "')";
	        $result = api_sql_query($sql,__FILE__,__LINE__);
	        
	        if ( mysql_num_rows($result) > 0)
	        {
		        $remainingUsers = '';
		        while ( ($res = mysql_fetch_array($result)))
		        {
					$remainingUsers .= ', ' . htmlspecialchars($res[0].' '.$res[1]);
		        }
		        $errormsg .= '<br>' . dropbox_lang("mailingNothingFor") . substr($remainingUsers, 1) . '.<br>';
	        }
	        
			if (get_url_param('mailingSend', '^yes$'))
			{
				chdir(dropbox_cnf("sysPath"));
				$zipFile->extract(PCLZIP_OPT_REMOVE_ALL_PATH);
				
				$mailingPseudoId = dropbox_cnf("mailingIdBase") + $mailing_item->id;

				foreach( $goodFiles as $thisFile => $thisRecip)
				{
		            if ( is_string($thisRecip))  // remove problem file
		            {
			            @unlink(dropbox_cnf("sysPath") . '/' . $thisFile);
		            }
		            else
		            {
				        $newName = getLoginFromId( api_get_user_id()) . "_" . $thisFile . "_" . uniqid('');
				        if ( rename(dropbox_cnf("sysPath") . '/' . $thisFile, dropbox_cnf("sysPath") . '/' . $newName))
							new Dropbox_SentWork( $mailingPseudoId, $thisFile, $mailing_item->description, $mailing_item->author, $newName, $thisContent['size'], array($thisRecip[0]));
		            }
				}
				
			    $sendDT = addslashes(date("Y-m-d H:i:s",time()));
			    // set filesize to zero on send, to avoid 2nd send (see index.php)
				$sql = "UPDATE ".dropbox_cnf("fileTbl")."
						SET filesize = '0'
						, upload_date = '".$sendDT."', last_upload_date = '".$sendDT."'
						WHERE id='".addslashes($mailing_item->id)."'";
				$result =api_sql_query($sql,__FILE__,__LINE__);
			}
			elseif ( $mailing_item->filesize != 0)
			{
		        $errormsg .= '<br>' . dropbox_lang("mailingNotYetSent") . '<br>';
			}
        }
    }
    else
    {
        $error = TRUE; $errormsg .= dropbox_lang("mailingWrongZipfile");
    }
	
	
    /**
     * -------------------------------------
     * EXAMINE OR SEND MAILING RESULTMESSAGE
     * -------------------------------------
     */
    if ( $error)
    {
        ?>
		<b><font color="#FF0000"><?php echo $errormsg?></font></b><br><br>
		<?php
		echo_go_back();
    }

    else
    {
        ?>
		<?php echo $errormsg?><br><br>
		<?php
		echo_go_back();
    }

	if ($origin != 'learnpath') { //so we are not in learnpath tool
	   Display::display_footer();
	}

    exit;
}


/**
 * ====================
 * SHOW / EDIT FEEDBACK
 * ====================
 */
 
if ( isset( $_GET['showFeedback']) || isset( $_GET['editFeedback']))
{
    if ($origin != 'learnpath')
    {
        Display::display_header($nameTools,"Dropbox");
    }
    else
    {
    	echo '<link rel="stylesheet" type="text/css" href="', 
    	    api_get_path(WEB_CODE_PATH), 'css/default.css">';
    }
    
    api_display_tool_title($nameTools);

	if (($mailing = get_url_param('mailing')))
	{
		getUserOwningThisMailing($mailing, api_get_user_id(), '408');  // or die
		$dropbox_person = new Dropbox_Person($mailing, $is_courseAdmin, $is_courseTutor);
	}
	else
	{
	    $dropbox_person = new Dropbox_Person( api_get_user_id(), $is_courseAdmin, $is_courseTutor);
    }

    if (($id = get_url_param('showFeedback')))
    {
		$w = new Dropbox_SentWork($id);
		
		if ($w->uploader_id != api_get_user_id())
		    getUserOwningThisMailing($w->uploader_id, api_get_user_id(), '413');  // or die
    	
		echo '<h4>', htmlspecialchars($w->title), '</h4>';
		
    	foreach ($w -> recipients as $r) if (($fb = $r["feedback"]))
    	{
            $fbarray [$r["feedback_date"].$r["name"]]= 
                $r["name"] . ' ' . dropbox_lang("sentOn", "noDLTT") . 
                ' ' . $r["feedback_date"] . ":\n" . $fb;
    	}
    	
    	if ($fbarray)
    	{
        	krsort($fbarray);
            echo '<textarea class="dropbox_feedbacks">',
                    htmlspecialchars(implode("\n\n", $fbarray)), '</textarea>', "\n";
        }
        else
        {
            echo '<textarea class="dropbox_feedbacks">&nbsp;</textarea>', "\n";
        }
        
        $tellUser = dropbox_lang("showFeedback"); $can_return = TRUE;
    }
    elseif (($id = get_url_param('editFeedback')))
    {
		if (!isset($dropbox_person->receivedWork[$id])) die(dropbox_lang("generalError")." (code 414)");
		$w = $dropbox_person->receivedWork[$id];

		echo '<h4>', htmlspecialchars($w->title);
		
        if ($w->feedback) echo ' (', htmlspecialchars($w->feedback_date), ')';

        echo '</h4><form method="post" action="index.php">', "\n", 
            '<input type="hidden" name="feedbackid" value="', 
                $id, '"/>', "\n", 
            '<textarea name="feedbacktext" class="dropbox_feedbacks">',
                htmlspecialchars($w->feedback), '</textarea>', "<br>\n", 
            '<input type="submit" name="feedbacksubmit" value="', dropbox_lang("ok", "noDLTT"), '"/>', "\n", 
            '</form>', "\n";
        $tellUser = dropbox_lang("giveFeedback"); $can_return = TRUE;
    }

    /**
     * --------------------------
     * RESULTMESSAGE FOR FEEDBACK
     * --------------------------
     */
    echo $tellUser, '<br><br>';
    
    if (($mailing = get_url_param('mailing'))) 
        echo dropbox_link('index', URL_OR.'&mailing='.$mailing, 
            dropbox_lang("backList").': '.htmlspecialchars(getUserNameFromId($mailing))), 
            '<br>';
    
    if ($can_return && ($fid = get_url_param('fid', '^([0-9]+|-2|-1)$')) != '' && 
            ($fid < 0 || ($folder = $dropbox_person->folders[$fid]))) 
        echo dropbox_link('dropbox_folder', URL_OR.'&fid='.$fid, 
            dropbox_lang("backList").': ' . 
            ($fid == -2 ? dropbox_lang('receivedTitle') : ($fid == -1 ? dropbox_lang('sentTitle') : 
                htmlspecialchars($folder)))), 
            '<br>';
    
	echo_go_back();
    
	if ($origin != 'learnpath')
	{
	    Display::display_footer();
	}

    exit;
}

/**
 * ============
 * DELETE FILES
 * ============
 * - DELETE ALL RECEIVED / ALL SENT / COMPLETE FOLDER
 * - DELETE 1 RECEIVED / SENT / FILED
 * Note: only output in case of error, otherwise redirect to calling script
 */
 
if ( isset( $_GET['deleteReceived']) || isset( $_GET['deleteSent'])
         || isset( $_GET['deleteAllFiled']) || isset( $_GET['deleteFiled']))
{
	if (($mailing = get_url_param('mailing')))
	{
		getUserOwningThisMailing($mailing, api_get_user_id(), '408');  // or die
		$dropbox_person = new Dropbox_Person($mailing, $is_courseAdmin, $is_courseTutor);
	}
	else
	{
	    $dropbox_person = new Dropbox_Person( api_get_user_id(), $is_courseAdmin, $is_courseTutor);
    }

	$can_return = TRUE;
	
    if (($del = get_url_param('deleteReceived', '^all|[0-9]+$')))
    {
        if ($del == "all")
        {
            if (($dropbox_user_filter = get_url_param('dropbox_user_filter')))
            	$dropbox_person->filter_received_work('uploader_id',$dropbox_user_filter);
            $dropbox_person->deleteAllReceivedWork();
        }
        else
        {
            $dropbox_person->deleteReceivedWork($del);
        }
    }
    elseif (($del = get_url_param('deleteSent', '^all|[0-9]+$')))
    {
        if ($del == "all")
        {
            $dropbox_person->deleteAllSentWork();
        }
        else
        {
            $dropbox_person->deleteSentWork($del);
        }
    }
    elseif (($del = get_url_param('deleteAllFiled')) != '')
    {
        if (!($folder = $dropbox_person->folders[$del]))
            die(dropbox_lang("generalError")." (code 411)");
        $dropbox_person->deleteAllFiledWork($folder);
        $can_return = FALSE;  // cannot return to folder that no longer exists
    }
    elseif (($del = get_url_param('deleteFiled')))
    {
        $nf = count($dropbox_person->folders);
        $dropbox_person->deleteFiledWork($del);
        $can_return = count($dropbox_person->folders) == $nf;
    }

    /**
     * ------------------------
     * RESULTMESSAGE FOR DELETE
     * ------------------------
     */
    
    if (($mailing = get_url_param('mailing')))
    {
        header("Location: http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? "s" : "") . 
            "://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php".URL_OR.'&mailing='.$mailing);
    	exit;  // instead of header + echo dropbox_lang("fileDeleted"), '<br><br>'; + code below
        echo dropbox_link('index', URL_OR.'&mailing='.$mailing, 
            dropbox_lang("backList").': '.htmlspecialchars(getUserNameFromId($mailing))), '<br>';
    } 
    
    if ($can_return && ($fid = get_url_param('fid', '^([0-9]+|-2|-1)$')) != '' && 
            ($fid < 0 || ($folder = $dropbox_person->folders[$fid])))
    {
        header("Location: http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? "s" : "") . 
            "://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/dropbox_folder.php".URL_OR.'&fid='.$fid);
    	exit;  // instead of header + echo dropbox_lang("fileDeleted"), '<br><br>'; + code below
        echo dropbox_link('dropbox_folder', URL_OR.'&fid='.$fid, 
            dropbox_lang("backList").': ' . 
            ($fid == -2 ? dropbox_lang('receivedTitle') : ($fid == -1 ? dropbox_lang('sentTitle') : 
                htmlspecialchars($folder)))), '<br>';
    } 
    
    header("Location: http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? "s" : "") . 
        "://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
	exit;  // instead of header + echo dropbox_lang("fileDeleted"), '<br><br>'; + code below
}

?>
