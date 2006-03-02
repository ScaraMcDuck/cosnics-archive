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
 * Handles downloads of files
 *
 * Direct downloading is prevented with a .htaccess file in the dropbox directory
 * 
 * NOTE : 
 * When testing this with PHP4.0.4 on WinXP and Apache2 there were problems with downloading in IE6
 * After searching the only explanation I could find is a problem with the headers:
 * 
 * HEADERS SENT WITH PHP4.3:
 * HTTP/1.1·200·OK(CR)
 * (LF)
 * Date:·Fri,·12·Sep·2003·19:07:33·GMT(CR)
 * (LF)
 * Server:·Apache/2.0.47·(Win32)·PHP/4.3.3(CR)
 * (LF)
 * X-Powered-By:·PHP/4.3.3(CR)
 * (LF)
 * Set-Cookie:·PHPSESSID=06880edcc8363be3f60929576fc1bc6e;·path=/(CR)
 * (LF)
 * Expires:·Thu,·19·Nov·1981·08:52:00·GMT(CR)
 * (LF)
 * Cache-Control:·public(CR)
 * (LF)
 * Pragma:·(CR)
 * (LF)
 * Content-Transfer-Encoding:·binary(CR)
 * (LF)
 * Content-Disposition:·attachment;·filename=SV-262E4.png(CR)
 * (LF)
 * Content-Length:·92178(CR)
 * (LF)
 * Connection:·close(CR)
 * (LF)
 * Content-Type:·application/octet-stream(CR)
 * (LF)
 * (CR)
 * (LF)
 * 
 * HEADERS SENT WITH PHP4.0.4:
 * HTTP/1.1·200·OK(CR)
 * (LF)
 * Date:·Fri,·12·Sep·2003·18:28:21·GMT(CR)
 * (LF)
 * Server:·Apache/2.0.47·(Win32)(CR)
 * (LF)
 * X-Powered-By:·PHP/4.0.4(CR)
 * (LF)
 * Expires:·Thu,·19·Nov·1981·08:52:00·GMT(CR)
 * (LF)
 * Cache-Control:·no-store,·no-cache,·must-revalidate,·post-check=0,·pre-check=0,·,·public(CR)
 * (LF)
 * Pragma:·no-cache,·(CR)
 * (LF)
 * Content-Disposition:·attachment;·filename=SV-262E4.png(CR)
 * (LF)
 * Content-Transfer-Encoding:·binary(CR)
 * (LF)
 * Set-Cookie:·PHPSESSID=0a5b1c1b9d5e3b474fef359ee55e82d0;·path=/(CR)
 * (LF)
 * Content-Length:·92178(CR)
 * (LF)
 * Connection:·close(CR)
 * (LF)
 * Content-Type:·application/octet-stream(CR)
 * (LF)
 * (CR)
 * (LF)
 * 
 * As you can see the there is a difference in the Cache-Control directive. I suspect that this
 * explains the problem. Also take a look at http://bugs.php.net/bug.php?id=16458.
 * 
 * @author Jan Bols, original design and implementation
 * @author Rene Haentjens, mailing, feedback, folders, user-sortable tables
 * @author Roan Embrechts, virtual course support
 * @author Patrick Cool, config settings, tool introduction and refactoring
 * @package dokeos.dropbox
==============================================================================
*/

/*
==============================================================================
		INITIALISING VARIABLES
==============================================================================
*/ 
require_once( "dropbox_init.inc.php");


/*
==============================================================================
		SANITY CHECKS OF GET DATA & FILE
==============================================================================
*/ 
is_numeric($id = get_url_param('id', '^[0-9]+$', '*')) 
    or die(dropbox_lang("generalError")." (code 501)");

$work = new Dropbox_work($id);

$path = dropbox_cnf("sysPath") . "/" . $work -> filename; //path to file as stored on server
$file = $work->title;

// check that this file exists and that it doesn't include any special characters
//if ( !is_file( $path) || ! eregi( '^[A-Z0-9_\-][A-Z0-9._\-]*$', $file))
if ( !is_file( $path))
{
    die(dropbox_lang("generalError")." (code 504)");
}

/*
==============================================================================
		SEND HEADERS
==============================================================================
*/
require_once(api_get_library_path() . '/document.lib.php');
$mimetype = DocumentManager::file_get_mime_type(TRUE);

$fileparts = explode( '.', $file);
$filepartscount = count( $fileparts);

if ( ( $filepartscount > 1) && isset( $mimetype[$fileparts [$filepartscount - 1]]))
{ 
    // give hint to browser about filetype
    header( "Content-type: " . $mimetype[$fileparts [$filepartscount - 1]] . "\n");
}
else
{ 
	//no information about filetype: force a download dialog window in browser
	header( "Content-type: application/octet-stream\n");
}

if(!in_array($fileparts [$filepartscount - 1],array('doc','xls','ppt','pps','sxw','sxc','sxi')))
{
	header('Content-Disposition: inline; filename='.$file); // bugs with open office
}
else
{
	header('Content-Disposition: attachment; filename='.$file);
}


/**
 * Note that if you use these two headers from a previous example:
 * header('Cache-Control: no-cache, must-revalidate');
 * header('Pragma: no-cache');
 * before sending a file to the browser, the "Open" option on Internet Explorer's file download dialog will not work properly. If the user clicks "Open" instead of "Save," the target application will open an empty file, because the downloaded file was not cached. The user will have to save the file to their hard drive in order to use it. 
 * Make sure to leave these headers out if you'd like your visitors to be able to use IE's "Open" option.
 */
header( "Pragma: \n");
header( "Cache-Control: \n");
header( "Cache-Control: public\n"); // IE cannot download from sessions without a cache


/*if ( isset( $_SERVER["HTTPS"]))
{
    /**
     * We need to set the following headers to make downloads work using IE in HTTPS mode.
     *
    //header( "Pragma: ");
    //header( "Cache-Control: ");
    header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
    header( "Last-Modified: " . gmdate( "D, d M Y H:i:s") . " GMT\n");
    header( "Cache-Control: no-store, no-cache, must-revalidate\n"); // HTTP/1.1
    header( "Cache-Control: post-check=0, pre-check=0\n", false);
}*/



header( "Content-Description: " . trim( htmlentities( $file)) . "\n");
header( "Content-Transfer-Encoding: binary\n");
header( "Content-Length: " . filesize( $path)."\n" );

/*
==============================================================================
		SEND FILE
==============================================================================
*/
$fp = fopen( $path, "rb");
fpassthru( $fp);
exit( );

/**
 * Found a workaround to another headache that just cropped up tonight.  Apparently Opera 6.1 on Linux (unsure of other versions/platforms) has problems downloading files using the above methods if you have enabled compression via zlib.output_compression in php.ini.
 * It seems that Opera sees that the actual transfer size is less than the size in the "Content-length" header for the download and decides that the transfer was incomplete or corrupted.  It then either continuously retries the download or else leaves you with a corrupted file.
 * Solution:  Make sure your download script/section is off in its own directory. and add the following to your .htaccess file for that directory:
 * php_flag zlib.output_compression off
 */
?>