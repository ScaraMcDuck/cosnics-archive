<?php // $Id$
/**
 * replaces "forbidden" characters in a filename string
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @author - Ren�Haentjens, UGent (RH)
 * @param  - string $filename
 * @param  - string $strict (optional) remove all non-ASCII
 * @return - the cleaned filename
 */

function replace_dangerous_char($filename, $strict = 'loose')
{
	$filename = ereg_replace("\.+$", "", substr(strtr(ereg_replace(
	    "[^!-~\x80-\xFF]", "_", trim($filename)), '\/:*?"<>|\'',
        /* Keep C1 controls for UTF-8 streams */  '-----_---_'), 0, 250));
	if ($strict != 'strict') return $filename;

	return ereg_replace("[^!-~]", "x", $filename);
}

//------------------------------------------------------------------------------

/**
 * change the file name extension from .php to .phps
 * Useful to secure a site !!
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - fileName (string) name of a file
 * @return - the filenam phps'ized
 */

function php2phps ($fileName)
{
	$fileName = eregi_replace("\.(php.?|phtml)$", ".phps", $fileName);
	return $fileName;
}



/**
 * Try to add an extension to files without extension
 * Some applications on Macintosh computers don't add an extension to the files.
 * This subroutine try to fix this on the basis of the MIME type sent
 * by the browser.
 *
 * Note : some browsers don't send the MIME Type (e.g. Netscape 4).
 *        We don't have solution for this kind of situation
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @author - Bert Vanderkimpen
 * @param  - fileName (string) - Name of the file
 * @param  - fileType (string) - Type of the file
 * @return - fileName (string)
 *
 */

function add_ext_on_mime($fileName,$fileType)
{
	/*
	 * Check if the file has an extension AND if the browser has sent a MIME Type
	 */

	if(!ereg("([[:alnum:]]|[[[:punct:]])+\.[[:alnum:]]+$", $fileName)
		&& $fileType)
	{
		/*
		 * Build a "MIME-types / extensions" connection table
		 */

		static $mimeType = array();

		$mimeType[] = "application/msword";             $extension[] =".doc";
		$mimeType[] = "application/rtf";                $extension[] =".rtf";
		$mimeType[] = "application/vnd.ms-powerpoint";  $extension[] =".ppt";
		$mimeType[] = "application/vnd.ms-excel";       $extension[] =".xls";
		$mimeType[] = "application/pdf";                $extension[] =".pdf";
		$mimeType[] = "application/postscript";         $extension[] =".ps";
		$mimeType[] = "application/mac-binhex40";       $extension[] =".hqx";
		$mimeType[] = "application/x-gzip";             $extension[] ="tar.gz";
		$mimeType[] = "application/x-shockwave-flash";  $extension[] =".swf";
		$mimeType[] = "application/x-stuffit";          $extension[] =".sit";
		$mimeType[] = "application/x-tar";              $extension[] =".tar";
		$mimeType[] = "application/zip";                $extension[] =".zip";
		$mimeType[] = "application/x-tar";              $extension[] =".tar";
		$mimeType[] = "text/html";                      $extension[] =".htm";
		$mimeType[] = "text/plain";                     $extension[] =".txt";
		$mimeType[] = "text/rtf";                       $extension[] =".rtf";
		$mimeType[] = "img/gif";                        $extension[] =".gif";
		$mimeType[] = "img/jpeg";                       $extension[] =".jpg";
		$mimeType[] = "img/png";                        $extension[] =".png";
		$mimeType[] = "audio/midi";                     $extension[] =".mid";
		$mimeType[] = "audio/mpeg";                     $extension[] =".mp3";
		$mimeType[] = "audio/x-aiff";                   $extension[] =".aif";
		$mimeType[] = "audio/x-pn-realaudio";           $extension[] =".rm";
		$mimeType[] = "audio/x-pn-realaudio-plugin";    $extension[] =".rpm";
		$mimeType[] = "audio/x-wav";                    $extension[] =".wav";
		$mimeType[] = "video/mpeg";                     $extension[] =".mpg";
		$mimeType[] = "video/quicktime";                $extension[] =".mov";
		$mimeType[] = "video/x-msvideo";                $extension[] =".avi";
		//test on PC (files with no extension get application/octet-stream)
		//$mimeType[] = "application/octet-stream";      $extension[] =".ext";

		/*
		 * Check if the MIME type sent by the browser is in the table
		 */

		foreach($mimeType as $key=>$type)
		{
			if ($type == $fileType)
			{
				$fileName .=  $extension[$key];
				break;
			}
		}

		unset($mimeType, $extension, $type, $key); // Delete to eschew possible collisions
	}

	return $fileName;
}

?>