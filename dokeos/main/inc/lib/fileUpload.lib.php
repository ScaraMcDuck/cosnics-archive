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

?>