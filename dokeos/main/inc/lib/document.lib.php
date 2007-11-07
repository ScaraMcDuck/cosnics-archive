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
	*	Get the content type of a file by checking the extension
	*	We could use mime_content_type() with php-versions > 4.3,
	*	but this doesn't work as it should on Windows installations
	*
	*	@param string $filename or boolean TRUE to return complete array
	*	@author ? first version
	*	@author Bert Vanderkimpen
	*
	*/
	function file_get_mime_type($filename)
	{
		//all mime types in an array (from 1.6, this is the authorative source)
		//please keep this alphabetical if you add something to this list!!!
		$mime_types = array ("ai" => "application/postscript", "aif" => "audio/x-aiff", "aifc" => "audio/x-aiff", "aiff" => "audio/x-aiff", "asf" => "video/x-ms-asf", "asc" => "text/plain", "au" => "audio/basic", "avi" => "video/x-msvideo", "bcpio" => "application/x-bcpio", "bin" => "application/octet-stream", "bmp" => "image/bmp", "cdf" => "application/x-netcdf", "class" => "application/octet-stream", "cpio" => "application/x-cpio", "cpt" => "application/mac-compactpro", "csh" => "application/x-csh", "css" => "text/css", "dcr" => "application/x-director", "dir" => "application/x-director", "djv" => "image/vnd.djvu", "djvu" => "image/vnd.djvu", "dll" => "application/octet-stream", "dmg" => "application/x-diskcopy", "dms" => "application/octet-stream", "doc" => "application/msword", "dvi" => "application/x-dvi", "dwg" => "application/vnd.dwg", "dxf" => "application/vnd.dxf", "dxr" => "application/x-director", "eps" => "application/postscript", "etx" => "text/x-setext", "exe" => "application/octet-stream", "ez" => "application/andrew-inset", "gif" => "image/gif", "gtar" => "application/x-gtar", "gz" => "application/x-gzip", "hdf" => "application/x-hdf", "hqx" => "application/mac-binhex40", "htm" => "text/html", "html" => "text/html", "ice" => "x-conference-xcooltalk", "ief" => "image/ief", "iges" => "model/iges", "igs" => "model/iges", "jar" => "application/java-archiver", "jpe" => "image/jpeg", "jpeg" => "image/jpeg", "jpg" => "image/jpeg", "js" => "application/x-javascript", "kar" => "audio/midi", "latex" => "application/x-latex", "lha" => "application/octet-stream", "lzh" => "application/octet-stream", "m1a" => "audio/mpeg", "m2a" => "audio/mpeg", "m3u" => "audio/x-mpegurl", "man" => "application/x-troff-man", "me" => "application/x-troff-me", "mesh" => "model/mesh", "mid" => "audio/midi", "midi" => "audio/midi", "mov" => "video/quicktime", "movie" => "video/x-sgi-movie", "mp2" => "audio/mpeg", "mp3" => "audio/mpeg", "mp4" => "video/mpeg4-generic", "mpa" => "audio/mpeg", "mpe" => "video/mpeg", "mpeg" => "video/mpeg", "mpg" => "video/mpeg", "mpga" => "audio/mpeg", "ms" => "application/x-troff-ms", "msh" => "model/mesh", "mxu" => "video/vnd.mpegurl", "nc" => "application/x-netcdf", "oda" => "application/oda", "pbm" => "image/x-portable-bitmap", "pct" => "image/pict", "pdb" => "chemical/x-pdb", "pdf" => "application/pdf", "pgm" => "image/x-portable-graymap", "pgn" => "application/x-chess-pgn", "pict" => "image/pict", "png" => "image/png", "pnm" => "image/x-portable-anymap", "ppm" => "image/x-portable-pixmap", "ppt" => "application/vnd.ms-powerpoint", "pps" => "application/vnd.ms-powerpoint", "ps" => "application/postscript", "qt" => "video/quicktime", "ra" => "audio/x-realaudio", "ram" => "audio/x-pn-realaudio", "rar" => "image/x-rar-compressed", "ras" => "image/x-cmu-raster", "rgb" => "image/x-rgb", "rm" => "audio/x-pn-realaudio", "roff" => "application/x-troff", "rpm" => "audio/x-pn-realaudio-plugin", "rtf" => "text/rtf", "rtx" => "text/richtext", "sgm" => "text/sgml", "sgml" => "text/sgml", "sh" => "application/x-sh", "shar" => "application/x-shar", "silo" => "model/mesh", "sib" => "application/X-Sibelius-Score", "sit" => "application/x-stuffit", "skd" => "application/x-koan", "skm" => "application/x-koan", "skp" => "application/x-koan", "skt" => "application/x-koan", "smi" => "application/smil", "smil" => "application/smil", "snd" => "audio/basic", "so" => "application/octet-stream", "spl" => "application/x-futuresplash", "src" => "application/x-wais-source", "sv4cpio" => "application/x-sv4cpio", "sv4crc" => "application/x-sv4crc", "svf" => "application/vnd.svf", "swf" => "application/x-shockwave-flash", "sxc" => "application/vnd.sun.xml.calc", "sxi" => "application/vnd.sun.xml.impress", "sxw" => "application/vnd.sun.xml.writer", "t" => "application/x-troff", "tar" => "application/x-tar", "tcl" => "application/x-tcl", "tex" => "application/x-tex", "texi" => "application/x-texinfo", "texinfo" => "application/x-texinfo", "tga" => "image/x-targa", "tif" => "image/tif", "tiff" => "image/tiff", "tr" => "application/x-troff", "tsv" => "text/tab-seperated-values", "txt" => "text/plain", "ustar" => "application/x-ustar", "vcd" => "application/x-cdlink", "vrml" => "model/vrml", "wav" => "audio/x-wav", "wbmp" => "image/vnd.wap.wbmp", "wbxml" => "application/vnd.wap.wbxml", "wml" => "text/vnd.wap.wml", "wmlc" => "application/vnd.wap.wmlc", "wmls" => "text/vnd.wap.wmlscript", "wmlsc" => "application/vnd.wap.wmlscriptc", "wma" => "video/x-ms-wma", "wmv" => "video/x-ms-wmv", "wrl" => "model/vrml", "xbm" => "image/x-xbitmap", "xht" => "application/xhtml+xml", "xhtml" => "application/xhtml+xml", "xls" => "application/vnd.ms-excel", "xml" => "text/xml", "xpm" => "image/x-xpixmap", "xsl" => "text/xml", "xwd" => "image/x-windowdump", "xyz" => "chemical/x-xyz", "zip" => "application/zip");

		if ($filename === TRUE)
			return $mime_types;

		//get the extension of the file
		$extension = explode('.', $filename);

		//$filename will be an array if a . was found
		if (is_array($extension))
		{
			$extension = (strtolower($extension[sizeof($extension) - 1]));
		}
		//file without extension
		else
		{
			$extension = 'empty';
		}

		//if the extension is found, return the content type
		if (isset ($mime_types[$extension]))
			return $mime_types[$extension];
		//else return octet-stream
		return "application/octet-stream";
	}



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