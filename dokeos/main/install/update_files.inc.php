<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

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
* Updates the Dokeos files from an older version
* IMPORTANT: This script has to be included by install/index.php and update_courses.php
*
* DOKEOS_INSTALL is defined in the install/index.php
* DOKEOS_COURSE_UPDATE is defined in update_courses.php
*
* When DOKEOS_INSTALL or DOKEOS_COURSE_UPDATE is defined, do for every course:
* - remove the .htaccess in the document folder
* - remove the index.php in the group folder
* - write a new group/index.php file, make it an empty html file
* - remove the index.php of the course folder
* - write a new index.php file in the course folder, with some settings
* - create a 'temp' directory in the course folder
* - move the course folder inside the courses folder of the new Dokeos installation
* - move the group documents from the group folder to the document folder,
*   keeping subfolders intact
* - stores all documents inside the database (document and item_property tables)
* - remove the visibility field from the document table
* - update the item properties of the group documents
*
* Additionally, when DOKEOS_INSTALL is defined
* - write a config file, claro_main.conf.php, with important settings
* - write a .htaccess file (with instructions for Apache) in the courses directory
* - remove the new claroline/upload/users directory and rename the claroline/img/users
*   directory of the old version to claroline/upload/users
* - rename the old claro_main.conf.php to claro_main.conf.php.old,
*   or if this fails delete the old claro_main.conf.php
*
* @package dokeos.install
==============================================================================
*/
/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
* Function used to upgrade from 1.6.x versions to 1.7.
* @todo move the courses from the 1.6 installation courses folder
* to the new 1.7 installation courses folder
*/
function file_upgrade_v16_to_v17($urlAppendPath, $main_database)
{
	$main_course_table = "`$main_database`.`course`";
	$newPath = str_replace('\\', '/', realpath('../..')).'/';
	$oldPath = $_POST['updatePath'];
	
	if (defined('DOKEOS_INSTALL'))
	{
		// Write the Dokeos config file
		write_dokeos_config_file($newPath.'claroline/inc/conf/claro_main.conf.php');
		// Write a distribution file with the config as a backup for the admin
		write_dokeos_config_file($newPath.'claroline/inc/conf/claro_main.conf.dist.php');
		// Write a .htaccess file in the course repository
		write_courses_htaccess_file($urlAppendPath);

		//rename or delete the config file of the old installation
		if (!@ rename($oldPath.'claroline/inc/conf/claro_main.conf.php', $oldPath.'claroline/inc/conf/claro_main.conf.old.php'))
		{
			unlink($oldPath.'claroline/inc/conf/claro_main.conf.php');
		}
		
		//move all courses from the old path to the new path
		$get_course_list_sql = "SELECT directory FROM $main_course_table WHERE target_course_code IS NULL";
		$sql_result = mysql_query($get_course_list_sql);
		while ( $this_course = mysql_fetch_array($sql_result) )
		{
			$course_folder = $this_course['directory'];
			rename($oldPath.'courses/'.$course_folder, $newPath.'courses/'.$course_folder);
		}
	}
}

/**
* Function used to upgrade from 1.5.x versions (1.5, 1.5.4, 1.5.5) to 1.6.
* Untested, simply moved code that was outside function to here.
* Guaranteed not to work - needs more input.
*/
function file_upgrade_v15_to_v16()
{
	$newPath = str_replace('\\', '/', realpath('../..')).'/';
	$oldPath = $_POST['updatePath'];

	foreach ($coursePath as $key => $course)
	{
		$mysql_base_course = $courseDB[$key];

		@ unlink($oldPath.$course.'/document/.htaccess');

		@ unlink($oldPath.$course.'/group/index.php');

		if ($fp = @ fopen($oldPath.$course.'/group/index.php', 'w'))
		{
			fputs($fp, '<html></html>');

			fclose($fp);
		}

		@ unlink($oldPath.$course.'/index.php');

		if ($fp = @ fopen($oldPath.$course.'/index.php', 'w'))
		{
			fputs($fp, '<?php
															$cidReq = "'.$key.'";
															$dbname = "'.str_replace($dbPrefixForm, '', $mysql_base_course).'";
									
															include("../../claroline/course_home/course_home.php");
															?>');

			fclose($fp);
		}

		@ mkdir($oldPath.$course.'/temp', 0777);

		@ rename($oldPath.$course, $newPath.'courses/'.$course);

		// Move group documents to document folder of the course
		$group_dir = $newPath.'courses/'.$course.'/group';

		if ($dir = @ opendir($group_dir))
		{
			while (($entry = readdir($dir)) !== false)
			{
				if ($entry != '.' && $entry != '..' && is_dir($group_dir.'/'.$entry))
				{
					$from_dir = $group_dir.'/'.$entry;
					$to_dir = $newPath.'courses/'.$course.'/document/'.$entry;

					@ rename($from_dir, $to_dir);
				}
			}

			closedir($dir);
		}

		fill_document_table($newPath.'courses/'.$course.'/document');

		mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."document` DROP `visibility`");

		// Update item_properties of group documents
		$sql = "SELECT d.id AS doc_id, g.id AS group_id FROM `$mysql_base_course".$dbGlu."group_info` g,`$mysql_base_course".$dbGlu."document` d WHERE path LIKE CONCAT(g.secret_directory,'%')";
		$res = mysql_query($sql);

		while ($group_doc = mysql_fetch_object($res))
		{
			$sql = "UPDATE `$mysql_base_course".$dbGlu."item_property` SET to_group_id = '".$group_doc->group_id."', visibility = '1' WHERE ref = '".$group_doc->doc_id."' AND tool = '".TOOL_DOCUMENT."'";
			mysql_query($sql);
		}
	}

	if (defined('DOKEOS_INSTALL'))
	{
		// Write the Dokeos config file
		write_dokeos_config_file($newPath.'claroline/inc/conf/claro_main.conf.php');
		// Write a distribution file with the config as a backup for the admin
		write_dokeos_config_file($newPath.'claroline/inc/conf/claro_main.conf.dist.php');
		// Write a .htaccess file in the course repository
		write_courses_htaccess_file($urlAppendPath);

		require_once ('../../common/filesystem/filesystem.class.php');
		// First remove the upload/users directory in the new installation
		Filesystem::remove($newPath.'claroline/upload/users');
		// Move the old user images to the new installation
		@ rename($oldPath.'claroline/img/users', $newPath.'claroline/upload/users');

		if (!@ rename($oldPath.'claroline/inc/conf/claro_main.conf.php', $oldPath.'claroline/inc/conf/claro_main.conf.old.php'))
		{
			unlink($oldPath.'claroline/inc/conf/claro_main.conf.php');
		}
	}
}

/**
* This function puts the documents of the upgraded courses
* into the necessary tables of the new version:
* the document and item_property tables.
*
* It is used in the upgrade from Dokeos 1.5.x versions to
* Dokeos 1.6
*
* @return boolean true if everything worked, false otherwise
*/
function fill_document_table($dir)
{
	global $newPath, $course, $mysql_base_course, $dbGlu;

	$documentPath = $newPath.'courses/'.$course.'/document';

	if (!@ $opendir = opendir($dir))
	{
		return false;
	}

	while ($readdir = readdir($opendir))
	{
		if ($readdir != '..' && $readdir != '.' && $readdir != '.htaccess')
		{
			$path = str_replace($documentPath, '', $dir.'/'.$readdir);
			$file_date = date("Y-m-d H:i:s", filemtime($dir.'/'.$readdir));

			if (is_file($dir.'/'.$readdir))
			{
				$file_size = filesize($dir.'/'.$readdir);

				$result = mysql_query("SELECT id,visibility FROM `$mysql_base_course".$dbGlu."document` WHERE path='".addslashes($path)."' LIMIT 0,1");

				if (list ($id, $visibility) = mysql_fetch_row($result))
				{
					mysql_query("UPDATE `$mysql_base_course".$dbGlu."document` SET filetype='file',title='".addslashes($readdir)."',size='$file_size' WHERE id='$id' AND path='".addslashes($path)."'");
				}
				else
				{
					mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."document`(path,filetype,title,size) VALUES('".addslashes($path)."','file','".addslashes($readdir)."','$file_size')");

					$id = mysql_insert_id();
				}

				$visibility = ($visibility == 'v') ? 1 : 0;

				mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."item_property`(tool,ref,visibility,lastedit_type,to_group_id,insert_date,lastedit_date) VALUES('document','$id','$visibility','DocumentAdded','0','".$file_date."','".$file_date."')");
			}
			elseif (is_dir($dir.'/'.$readdir))
			{
				$result = mysql_query("SELECT id,visibility FROM `$mysql_base_course".$dbGlu."document` WHERE path='".addslashes($path)."' LIMIT 0,1");

				if (list ($id, $visibility) = mysql_fetch_row($result))
				{
					mysql_query("UPDATE `$mysql_base_course".$dbGlu."document` SET filetype='folder',title='".addslashes($readdir)."' WHERE id='$id' AND path='".addslashes($path)."'");
				}
				else
				{
					mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."document`(path,filetype,title) VALUES('".addslashes($path)."','folder','".addslashes($readdir)."')");

					$id = mysql_insert_id();
				}

				$visibility = ($visibility == 'v') ? 1 : 0;

				mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."item_property`(tool,ref,visibility, lastedit_type, to_group_id,insert_date,lastedit_date) VALUES('document','$id','$visibility','FolderCreated','0','".$file_date."','".$file_date."')");

				if (!fill_document_table($dir.'/'.$readdir))
				{
					return false;
				}
			}
		}
	}

	closedir($opendir);

	return true;
}

/*
==============================================================================
		INIT SECTION
==============================================================================
*/

if (defined('DOKEOS_INSTALL') || defined('DOKEOS_COURSE_UPDATE'))
{
	file_upgrade_v16_to_v17($urlAppendPath, $dbNameForm);
}
else
{
	echo 'You are not allowed here !';
}
?>