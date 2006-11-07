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
 * 3 classes and one function interface the dropbox module to the database.
 * 
 * - Class Dropbox_Work:
 * 		. id
 * 		. uploader_id	    => who sent it: Dokeos user_id or mailing pseudo_id
 * 		. uploaderName
 * 		. filename		    => name of file stored on the server
 * 		. filesize		    => for mailing zipfile, set to zero on send
 * 		. title			    => original filename, spaces replaced by underscores
 * 		. description
 * 		. author
 * 		. upload_date	    => date when file was first sent
 * 		. last_upload_date  => date when file was last sent
 *  	. isOldWork 	    => has the work already been uploaded before
 *      . feedback_date     => date of most recent feedback
 *      . feedback          => feedback text
 *      . folder            => foldername (filed work) or empty
 * 
 * - Class Dropbox_SentWork extends Dropbox_Work
 * 		. recipients	    => mailing pseudo_id or array of recipients
 *                             ["id"]["name"]["feedback_date"]["feedback"]
 * - Class Dropbox_Person:
 * 		. userId
 * 		. receivedWork 	    => array of Dropbox_Work objects
 * 		. sentWork 		    => array of Dropbox_SentWork objects
 * 		. isCourseTutor
 * 		. isCourseAdmin
 *
 * - function getUserOwningThisMailing:
 *      A mailing zip-file is posted to (dest_user_id = ) mailing pseudo_id
 *      and it is only visible to its uploader (user_id).
 *      Mailing content files have uploader_id == mailing pseudo_id
 *      and a normal recipient;
 *      they are visible initially to recipient and pseudo_id.
 *
 * @author Jan Bols, original design and implementation
 * @author Rene Haentjens, mailing, feedback, folders, user-sortable tables
 * @author Roan Embrechts, virtual course support
 * @author Patrick Cool, config settings, tool introduction and refactoring
 * @package dokeos.dropbox
==============================================================================
*/


class Dropbox_Work
{
	var $id;
	var $uploader_id;
	var $uploaderName;
	var $filename;
	var $filesize;
	var $title;
	var $description;
	var $author;
	var $upload_date;
	var $last_upload_date;
	var $isOldWork;
	var $feedback_date, $feedback;
	var $folder;
	
	function Dropbox_Work ($arg1, $arg2=null, $arg3=null, $arg4=null, $arg5=null, $arg6=null)
	{
		/*
		* Constructor calls private functions to create a new work or retreive an existing work from DB
		* depending on the number of parameters
		*/
		if (func_num_args()>1)
		    $this->_createNewWork($arg1, $arg2, $arg3, $arg4, $arg5, $arg6);
		else
			$this->_createExistingWork($arg1);
	}
	
	function _createNewWork ($uploader_id, $title, $description, $author, $filename, $filesize)
	{
		/*
		* private function creating a new work object
		*/
		
		/*
		* Do some sanity checks
		*/
		settype($uploader_id, 'integer') or die(dropbox_lang("generalError")." (code 201)");
		//uploader must be coursemember to be able to upload
		//-->this check is done when submitting data so it isn't checked here
			
		/*
		* Fill in the properties
		*/
		$this->uploader_id = $uploader_id; 
		$this->uploaderName = getUserNameFromId($this->uploader_id);
		$this->filename = $filename;
		$this->filesize = $filesize;
		$this->title = $title;
		$this->description = $description;
		$this->author = $author;
		$this->last_upload_date = date("Y-m-d H:i:s",time());

		/*
		* Check if object exists already. If it does, the old object is used 
		* with updated information (authors, descriptio, upload_date)
		*/
		$this->isOldWork = FALSE;
		$sql="SELECT id, upload_date 
				FROM ".dropbox_cnf("fileTbl")." 
				WHERE filename = '".addslashes($this->filename)."'";
        $result = api_sql_query($sql,__FILE__,__LINE__);
		$res = mysql_fetch_array($result);
		if ($res != FALSE) $this->isOldWork = TRUE;
		
		/*
		* insert or update the dropbox_file table and set the id property
		*/
		if ($this->isOldWork)
		{
			$this->id = $res["id"];
			$this->upload_date = $res["upload_date"];
		    $sql = "UPDATE ".dropbox_cnf("fileTbl")."
					SET filesize = '".addslashes($this->filesize)."'
					, title = '".addslashes($this->title)."'
					, description = '".addslashes($this->description)."'
					, author = '".addslashes($this->author)."'
					, last_upload_date = '".addslashes($this->last_upload_date)."'
					WHERE id='".addslashes($this->id)."'";
			$result = api_sql_query($sql,__FILE__,__LINE__);
		}
		else
		{
			$this->upload_date = $this->last_upload_date;
			$sql="INSERT INTO ".dropbox_cnf("fileTbl")." 
				(uploader_id, filename, filesize, title, description, author, upload_date, last_upload_date)
				VALUES ('".addslashes($this->uploader_id)."'
						, '".addslashes($this->filename)."'
						, '".addslashes($this->filesize)."'
						, '".addslashes($this->title)."'
						, '".addslashes($this->description)."'
						, '".addslashes($this->author)."'
						, '".addslashes($this->upload_date)."'
						, '".addslashes($this->last_upload_date)."'
						)";

        	$result = api_sql_query($sql,__FILE__,__LINE__);		
			$this->id = mysql_insert_id(); //get automatically inserted id
		}
		
		
		/*
		* insert entries into person table
		*/
		$sql="INSERT INTO ".dropbox_cnf("personTbl")." 
				(file_id, user_id)
				VALUES ('".addslashes($this->id)."'
						, '".addslashes($this->uploader_id)."'
						)";
        $result = api_sql_query($sql);	//if work already exists no error is generated
	}
	
	function _createExistingWork ($id)
	{
		/*
		* private function creating existing object by retreiving info from db
		*/
		
		/*
		* Do some sanity checks
		*/
		settype($id, 'integer') or die(dropbox_lang("generalError")." (code 205)"); //set $id to correct type

		/*
		* get the data from DB
		*/
		$sql="SELECT uploader_id, filename, filesize, title, description, author, upload_date, last_upload_date
				FROM ".dropbox_cnf("fileTbl")."
				WHERE id='".addslashes($id)."'";
        $result = api_sql_query($sql,__FILE__,__LINE__);
		$res = mysql_fetch_array($result,MYSQL_ASSOC);
		
		/*
		* Check if uploader is still in claroline system
		*/
		$uploader_id = stripslashes($res["uploader_id"]);    
		$uploaderName = getUserNameFromId($uploader_id);
		if ($uploaderName == FALSE)
		{
			//deleted user
			$this->uploader_id = -1;
			$this->uploaderName = dropbox_lang("anonymous", "noDLTT");			
		}
		else
		{
			$this->uploader_id = $uploader_id; 
			$this->uploaderName = $uploaderName;			
		}
		
		/*
		* Fill in properties
		*/
		$this->id = $id;
		$this->filename = stripslashes($res["filename"]);
		$this->filesize = stripslashes($res["filesize"]);
		$this->title = stripslashes($res["title"]);
		$this->description = stripslashes($res["description"]);
		$this->author = stripslashes($res["author"]);
		$this->upload_date = stripslashes($res["upload_date"]);
		$this->last_upload_date = stripslashes($res["last_upload_date"]);
		
		$result = api_sql_query("SELECT feedback_date, feedback FROM ".
		    dropbox_cnf("postTbl")." WHERE dest_user_id='".api_get_user_id().
		    "' AND file_id='".$id."'",__FILE__,__LINE__);		
		if ($res = mysql_fetch_array($result))
		{
    		$this->feedback_date = $res["feedback_date"];
    		$this->feedback = $res["feedback"];
		}  // do not fail if there is no recipient = current user...
		
		$result = api_sql_query("SELECT folder FROM ".
		    dropbox_cnf("personTbl")." WHERE user_id='".api_get_user_id().
		    "' AND file_id='".$id."'",__FILE__,__LINE__);		
		if ($res = mysql_fetch_array($result))
		{
    		$this->folder = $res["folder"];
		}
	}
}

class Dropbox_SentWork extends Dropbox_Work
{
	var $recipients;	//array of ["id"]["name"] arrays
	
	// Just upload: $recipients is set to array(uploader) if new, to empty array otherwise
	
	function Dropbox_SentWork ($arg1, $arg2=null, $arg3=null, $arg4=null, $arg5=null, $arg6=null, $arg7=null)
	{
		/*
		* Constructor calls private functions to create a new work or retreive an existing work from DB
		* depending on the number of parameters
		*/
		if (func_num_args()>1)
		    $this->_createNewSentWork ($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7);
		else
			$this->_createExistingSentWork ($arg1);
	}

	function _createNewSentWork ($uploader_id, $title, $description, $author, $filename, $filesize, $recipient_ids)
	{
		/*
		* private function creating a new SentWork object
		*
		* Mailing zip: $recipient_ids is big integer instead of array (see submit)
		*/

		/*
		* Call constructor of Dropbox_Work object
		*/
		$this->Dropbox_Work($uploader_id, $title, $description, $author, $filename, $filesize);

		/*
		* Do sanity checks on recipient_ids array & property fillin
		* The sanity check for ex-coursemembers is already done in base constructor
		*/
		settype($uploader_id, 'integer') or die(dropbox_lang("generalError")." (code 208)"); //set $uploader_id to correct type
		
		$justSubmit = FALSE;  // mailing zip-file or just upload
		if ( is_int($recipient_ids))
		{
			$justSubmit = TRUE; $recipient_ids = array($recipient_ids + $this->id);
			// Mailing pseudo-id = dropbox_cnf('mailingIdBase') + file_id
		}
		elseif ( count($recipient_ids) == 0)  // Just Upload: posted to self
		{
			$justSubmit = TRUE; $recipient_ids = array($uploader_id);
		}
		if (! is_array($recipient_ids) || count($recipient_ids) == 0) die(dropbox_lang("generalError")." (code 209)");
		foreach ($recipient_ids as $rec)
		{
			if (empty($rec)) die(dropbox_lang("generalError")." (code 210)");
			//cannot sent document to someone  who is not course member
			//-->this check is done when validating submitted data
			$this->recipients[] = array("id"=>$rec, "name"=>getUserNameFromId($rec));
		}
		
		/*
		* insert data in dropbox_post and dropbox_person table for each recipient
		*/
		foreach ($this->recipients as $rec)
		{	
			$sql="INSERT INTO ".dropbox_cnf("postTbl")." 
				(file_id, dest_user_id)
				VALUES ('".addslashes($this->id)."', '".addslashes($rec["id"])."')";
	        $result = api_sql_query($sql);	//if work already exists no error is generated
						
			//insert entries into person table
			$sql="INSERT INTO ".dropbox_cnf("personTbl")." 
				(file_id, user_id)
				VALUES ('".addslashes($this->id)."'
						, '".addslashes($rec["id"])."'
						)";
        	// do not add recipient in person table if mailing zip or just upload
			if (!$justSubmit) $result = api_sql_query($sql);	//if work already exists no error is generated

			//update item_property (previously last_tooledit) table for each recipient
			
			global $_course;
			
			if (($ownerid = $this->uploader_id) > dropbox_cnf("mailingIdBase"))
			    $ownerid = getUserOwningThisMailing($ownerid);
			if (($recipid = $rec["id"]) > dropbox_cnf("mailingIdBase"))
			    $recipid = $ownerid;  // mailing file recipient = mailing id, not a person
			dropbox_property_update($_course, TOOL_DROPBOX, $this->id, "DropboxFileAdded", $ownerid, NULL, $recipid) ;
		}
	}
	
	function _createExistingSentWork  ($id)
	{
		/*
		* private function creating existing object by retreiving info from db
		*/

		/*
		* Call constructor of Dropbox_Work object
		*/
		$this->Dropbox_Work($id);
		
		/*
		* Do sanity check
		* The sanity check for ex-coursemembers is already done in base constructor
		*/
		settype($id, 'integer') or die(dropbox_lang("generalError")." (code 211)"); //set $id to correct type

		/*
		* Fill in recipients array
		*/
		$this->recipients = array();
		$sql="SELECT dest_user_id, feedback_date, feedback 
				FROM ".dropbox_cnf("postTbl")."
				WHERE file_id='".addslashes($id)."'";
        $result = api_sql_query($sql,__FILE__,__LINE__);
		while ($res = mysql_fetch_array($result))
		{
			/*
			* check for deleted users
			*/
			$dest_user_id = $res["dest_user_id"];
			$recipientName = getUserNameFromId($dest_user_id);
			if ($recipientName == FALSE)
			{
				$this->recipients[] = array("id"=>-1, "name"=> dropbox_lang("anonymous", "noDLTT"));
			}
			elseif ($dest_user_id != $this->uploader_id)
			{
				$this->recipients[] = array("id"=>$dest_user_id, "name"=>$recipientName,
				    "feedback_date"=>$res["feedback_date"], "feedback"=>$res["feedback"]);
			}
		}
	}
}

class Dropbox_Person
{
	var $receivedWork;	//array: $file_id => Dropbox_Work object
	var $sentWork;		//array: $file_id => Dropbox_SentWork object
	var $userId = 0;
	var $isCourseAdmin = FALSE;
	var $isCourseTutor = FALSE;
	var $folders = array();

	function Dropbox_Person ($userId, $isCourseAdmin, $isCourseTutor)
	{
		/*
		* Constructor for creating the Dropbox_Person object
		*/
		
		/*
		* Fill in properties
		*/
		$this->userId = $userId;
		$this->isCourseAdmin = $isCourseAdmin;
		$this->isCourseTutor = $isCourseTutor;	
		$this->receivedWork = array();
		$this->sentWork = array();

		//Note: perhaps include an ex coursemember check to delete old files
		
		/*
		* find all entries where this person is the recipient but not the uploader
		*/
		$sql = "SELECT r.file_id, p.folder 
				FROM 
					".dropbox_cnf("postTbl")." r
					, ".dropbox_cnf("fileTbl")." f
					, ".dropbox_cnf("personTbl")." p
				WHERE r.dest_user_id = '".addslashes($this->userId)."' 
					AND r.file_id = f.id
					AND f.uploader_id !=  '".addslashes($this->userId)."'
					AND r.dest_user_id = p.user_id
					AND r.file_id = p.file_id";
        $result = api_sql_query($sql,__FILE__,__LINE__);
		while ($res = mysql_fetch_array($result))
		{
			$id = $res["file_id"];
    		$this->receivedWork[$id] = new Dropbox_Work($id);
			if (($folder = $res["folder"])) $this->folders[] = $folder;
		}
		
		/*
		* find all entries where this person is the sender/uploader
		*/
		$sql = "SELECT f.id, p.folder 
				FROM ".dropbox_cnf("fileTbl")." f, ".dropbox_cnf("personTbl")." p 
				WHERE f.uploader_id = '".addslashes($this->userId)."'
				AND f.uploader_id = p.user_id
				AND f.id = p.file_id";
        $result =api_sql_query($sql,__FILE__,__LINE__);
		while ($res = mysql_fetch_array($result))
		{
			$id = $res["id"];
			$this->sentWork[$id] = new Dropbox_SentWork($id);
			if (($folder = $res["folder"])) $this->folders[] = $folder;
		}
		// Just uploaded work is both received and sent
		// Filed work (in a folder) is also received or sent or both
		
		$this->recalcFolders();
	}
	
	
	function deleteAllReceivedWork ()
	{
		/*
		* Deletes all the unfiled received work of this person
		*/
	
		//delete entries in person table concerning received work
		foreach ($this->receivedWork as $id => $w) if (!$w->folder)
		{
			api_sql_query("DELETE FROM ".dropbox_cnf("personTbl") . 
			    " WHERE user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);
			unset($this->receivedWork[$id]);
		}
		$this->removeUnusedFiles();	//check for unused files
	}
	
	function deleteReceivedWork ($id)
	{
		/*
		* Deletes a received work of this person with id=$id
		*/

		//id check
		if (!isset($this->receivedWork[$id])) die(dropbox_lang("generalError")." (code 216)");
		
		//delete entries in person table concerning received work
		api_sql_query("DELETE FROM ".dropbox_cnf("personTbl") . 
		    " WHERE user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);
		
		unset($this->receivedWork[$id]);
		$this->removeUnusedFiles();	//check for unused files
	}
	
	function deleteAllSentWork ()
	{
		/*
		* Deletes all the unfiled sent work of this person
		*/
	
		//delete entries in person table concerning sent work
		foreach ($this->sentWork as $id => $w) if (!$w->folder)
		{
			api_sql_query("DELETE FROM ".dropbox_cnf("personTbl") . 
			    " WHERE user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);
			unset($this->sentWork[$id]);
			$this->removeMoreIfMailing($id);
		}		
		$this->removeUnusedFiles();	//check for unused files
	}
	
	function deleteSentWork ($id)
	{
		/*
		* Deletes a sent work of this person with id=$id
		*/

		//index check
		if (!isset($this->sentWork[$id]))  die(dropbox_lang("generalError")." (code 219)");
		
		//delete entries in person table concerning sent work
		api_sql_query("DELETE FROM ".dropbox_cnf("personTbl") . 
		    " WHERE user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);
		
		unset($this->sentWork[$id]);
		$this->removeMoreIfMailing($id);
		$this->removeUnusedFiles();	//check for unused files
	}
	
	function deleteAllFiledWork ($folder)
	{
		/*
		* Deletes all the work of this person filed in a specific folder
		*/
	    
		if (!$folder) return;
		
		//delete entries in person table concerning received work
		foreach ($this->receivedWork as $id => $w) if ($w->folder == $folder)
		{
			api_sql_query("DELETE FROM ".dropbox_cnf("personTbl") . 
			    " WHERE user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);
			unset($this->receivedWork[$id]);
		}
		//delete entries in person table concerning sent work
		foreach ($this->sentWork as $id => $w) if ($w->folder == $folder)
		{
			api_sql_query("DELETE FROM ".dropbox_cnf("personTbl") . 
			    " WHERE user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);
			unset($this->sentWork[$id]);
			$this->removeMoreIfMailing($id);
		}		
		$this->removeUnusedFiles();	//check for unused files
		$this->recalcFolders();
	}
	
	function deleteFiledWork ($id)
	{
		/*
		* Deletes a filed work (i.e. in a folder) of this person with id=$id
		*/

		//it must be either received work or sent work
		if (!isset($this->receivedWork[$id]))
		{
    		$this->deleteSentWork($id);
    		$this->recalcFolders();
    		return;
		}
		
		//delete entries in person table concerning received work
		api_sql_query("DELETE FROM ".dropbox_cnf("personTbl") . 
		    " WHERE user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);
		
		unset($this->receivedWork[$id]);
		$this->removeUnusedFiles();	//check for unused files
		$this->recalcFolders();
	}
	
    function removeUnusedFiles( )
    {
        // select all files that aren't referenced anymore
        $sql = "SELECT DISTINCT f.id, f.filename
    			FROM " . dropbox_cnf("fileTbl") . " f
    			LEFT JOIN " . dropbox_cnf("personTbl") . " p ON f.id = p.file_id
    			WHERE p.user_id IS NULL";
        $result = api_sql_query($sql,__FILE__,__LINE__);
        while ( $res = mysql_fetch_array( $result))
        {
    		//delete the selected files from the post and file tables
            $sql = "DELETE FROM " . dropbox_cnf("postTbl") . 
                " WHERE file_id='" . $res['id'] . "'";
            $result1 = api_sql_query($sql,__FILE__,__LINE__);
            $sql = "DELETE FROM " . dropbox_cnf("fileTbl") . 
                " WHERE id='" . $res['id'] . "'";
            $result1 = api_sql_query($sql,__FILE__,__LINE__);
    
    		//delete file from server
            @unlink( dropbox_cnf("sysPath") . "/" . $res["filename"]);
        }
    }
    
    function removeMoreIfMailing($file_id)
    {
        // when deleting a mailing zip-file (posted to mailingPseudoId):
        // 1. the detail window is no longer reachable, so
        //    for all content files, delete mailingPseudoId from person-table
        // 2. finding the owner (getUserOwningThisMailing) is no longer possible, so
        //    for all content files, replace mailingPseudoId by owner as uploader
    
        $sql = "SELECT p.dest_user_id
    			FROM " . dropbox_cnf("postTbl") . " p
    			WHERE p.file_id = '" . $file_id . "'";
        $result = api_sql_query($sql,__FILE__,__LINE__);
    
        if ( $res = mysql_fetch_array( $result))
        {
    	    $mailingPseudoId = $res['dest_user_id'];
    	    if ( $mailingPseudoId > dropbox_cnf("mailingIdBase"))
    	    {
    	        $sql = "DELETE FROM " . dropbox_cnf("personTbl") . 
    	            " WHERE user_id='" . $mailingPseudoId . "'";
    	        $result1 = api_sql_query($sql,__FILE__,__LINE__);
    	        
    	        $sql = "UPDATE " . dropbox_cnf("fileTbl") . 
    	            " SET uploader_id='" . api_get_user_id() . 
    	            "' WHERE uploader_id='" . $mailingPseudoId . "'";
    	        $result1 = api_sql_query($sql,__FILE__,__LINE__);
            }
        }
    }
    
	function recalcFolders()
	{
		/*
		* Recalculates person folders
		*/

		$this->folders = array();

		foreach($this->receivedWork as $w)
			if (($folder = $w->folder)) $this->folders[] = $folder;

		foreach($this->sentWork as $w)
			if (($folder = $w->folder)) $this->folders[] = $folder;

        $this->folders = array_unique($this->folders); sort($this->folders);
	}
	
	function fileReceivedWork($id, $folder = '')
	{
		$this->receivedWork[$id]->folder = $folder;
		$this->folderChange($id, $folder);		
	}
	
	function fileSentWork($id, $folder = '')
	{
		$this->sentWork[$id]->folder = $folder;
		$this->folderChange($id, $folder);		
	}
	
	function folderChange($id, $folder = '')
	{
		api_sql_query("UPDATE ".dropbox_cnf("personTbl")." SET folder='".
		    addslashes($folder)."' WHERE user_id='".$this->userId.
		    "' AND file_id='".$id."'",__FILE__,__LINE__);
	}
	
	function updateFeedback($id, $text)
	{
		/*
		* Updates feedback for received work of this person with id=$id
		*/

		//id check
		if (!isset($this->receivedWork[$id])) die(dropbox_lang("generalError")." (code 221)");
		
		$feedback_date = date("Y-m-d H:i:s",time());
		$this->receivedWork[$id]->feedback_date = $feedback_date;
		$this->receivedWork[$id]->feedback = $text;
		
		api_sql_query("UPDATE ".dropbox_cnf("postTbl")." SET feedback_date='".
		    addslashes($feedback_date)."', feedback='".addslashes($text).
		    "' WHERE dest_user_id='".$this->userId."' AND file_id='".$id."'",__FILE__,__LINE__);

		//make it (again) visible to the uploader
		api_sql_query("INSERT INTO ".dropbox_cnf("personTbl").
		    " (file_id, user_id) VALUES ('".addslashes($id)."'
			, '".addslashes($this->receivedWork[$id]->uploader_id)."')");
        //if this entry already exists no error is generated

		//update item_property (previously last_tooledit) table
		
		global $_course;
		
		if (($ownerid = $this->receivedWork[$id]->uploader_id) > dropbox_cnf("mailingIdBase"))
		    $ownerid = getUserOwningThisMailing($ownerid);
		dropbox_property_update($_course, TOOL_DROPBOX, $id, "DropboxFileUpdated", $this->userId, NULL, $ownerid) ;
	}
	
	/**
	 * Filter the received work
	 * @param string $type
	 * @param string $value
	 */
	function filter_received_work($type,$value)
	{
    	$new_received_work = array();
		foreach($this->receivedWork as $index => $work)
		{
			switch($type)
			{
				case 'uploader_id':
					if ($work->uploader_id == $value || 
					    ($work->uploader_id > dropbox_cnf("mailingIdBase") &&
					     getUserOwningThisMailing($work->uploader_id) == $value))
					{
						$new_received_work[$index] = $work;
					}
					break;
				default:
					$new_received_work[$index] = $work;	
			}
		}
		$this->receivedWork = $new_received_work;
	}
}


function getUserOwningThisMailing($mailingPseudoId, $owner = 0, $or_die = '')
{
    $sql = "SELECT f.uploader_id
			FROM " . dropbox_cnf("fileTbl") . " f
			LEFT JOIN " . dropbox_cnf("postTbl") . " p ON f.id = p.file_id
			WHERE p.dest_user_id = '" . $mailingPseudoId . "'";
    $result = api_sql_query($sql,__FILE__,__LINE__);

    if (!($res = mysql_fetch_array($result)))
        die(dropbox_lang("generalError")." (code 901)");
    
    if ($owner == 0) return $res['uploader_id'];
    
    if ($res['uploader_id'] == $owner) return TRUE;
    
    die(dropbox_lang("generalError")." (code ".$or_die.")");
}


function dropbox_property_update($_course, $tool, $item_id, $lastedit_type, $user_id, $to_group_id = 0, $to_user_id = NULL, $start_visible = 0, $end_visible = 0)
{
    if (function_exists('api_item_property_update'))  // Dokeos 1.7
         api_item_property_update($_course, $tool, $item_id, $lastedit_type, $user_id, $to_group_id, $to_user_id, $start_visible, $end_visible);
    elseif (function_exists('item_property_update'))  // Dokeos 1.6
         item_property_update($_course, $tool, $item_id, $lastedit_type, $user_id, $to_group_id, $to_user_id, $start_visible, $end_visible);
}
?>
