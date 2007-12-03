<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) Roan Embrechts, Vrije Universiteit Brussel

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
/*
==============================================================================
		DocumentManager CLASS
		the class and its functions
==============================================================================
*/

/**
 *	@package dokeos.library
 */
class DocumentManager
{
	/**
	* This function streams a file to the client
	*
	* @param string $full_file_name
	* @param boolean $forced
	* @param string $name
	* @return false if file doesn't exist, true if stream succeeded
	*/
	function file_send_for_download($full_file_name, $forced = false, $name = '')
	{
		if (!is_file($full_file_name))
		{
			return false;
		}
		$filename = ($name == '') ? basename($full_file_name) : $name;
		$len = filesize($full_file_name);

		if ($forced)
		{
			//force the browser to save the file instead of opening it

			header('Content-type: application/octet-stream');
			//header('Content-Type: application/force-download');
			header('Content-length: '.$len);
			if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
			{
				header('Content-Disposition: filename= '.$filename);
			}
			else
			{
				header('Content-Disposition: attachment; filename= '.$filename);
			}
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
			{
				header('Pragma: ');
				header('Cache-Control: ');
				header('Cache-Control: public'); // IE cannot download from sessions without a cache
			}
			header('Content-Description: '.$filename);
			header('Content-transfer-encoding: binary');

			$fp = fopen($full_file_name, 'r');
			fpassthru($fp);
			return true;
		}
		else
		{
			//no forced download, just let the browser decide what to do according to the mimetype

			$content_type = DocumentManager :: file_get_mime_type($filename);
			header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
			header('Content-type: '.$content_type);
			header('Content-Length: '.$len);
			$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
			if (strpos($user_agent, 'msie'))
			{
				header('Content-Disposition: ; filename= '.$filename);
			}
			else
			{
				header('Content-Disposition: inline; filename= '.$filename);
			}
			readfile($full_file_name);
			return true;
		}
	}

}
//end class DocumentManager
?>