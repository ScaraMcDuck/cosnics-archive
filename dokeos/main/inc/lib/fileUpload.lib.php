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


?>