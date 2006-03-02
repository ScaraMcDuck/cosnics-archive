<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	This is the text library for Dokeos.
*	Include/require it in your code to use its functionality.
*
*	@package dokeos.library
==============================================================================
*/

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
 * function make_clickable($string)
 *
 * @desc   completes url contained in the text with "<a href ...".
 *         However the function simply returns the submitted text without any
 *         transformation if it already contains some "<a href:" or "<img src=".
 * @param string $text text to be converted
 * @return text after conversion
 * @author Rewritten by Nathan Codding - Feb 6, 2001.
 *         completed by Hugues Peeters - July 22, 2002
 *
 * Actually this function is taken from the PHP BB 1.4 script
 * - Goes through the given string, and replaces xxxx://yyyy with an HTML <a> tag linking
 * 	to that URL
 * - Goes through the given string, and replaces www.xxxx.yyyy[zzzz] with an HTML <a> tag linking
 * 	to http://www.xxxx.yyyy[/zzzz]
 * - Goes through the given string, and replaces xxxx@yyyy with an HTML mailto: tag linking
 *		to that email address
 * - Only matches these 2 patterns either after a space, or at the beginning of a line
 *
 * Notes: the email one might get annoying - it's easy to make it more restrictive, though.. maybe
 * have it require something like xxxx@yyyy.zzzz or such. We'll see.
 */

function make_clickable($string)
{
	if(!stristr($string,' src=') && !stristr($string,' href='))
	{
		$string=eregi_replace("(https?|ftp)://([a-z0-9#?/&=._+:~%-]+)","<a href=\"\\1://\\2\" target=\"_blank\">\\1://\\2</a>",$string);
		$string=eregi_replace("([a-z0-9_.-]+@[a-z0-9.-]+)","<a href=\"mailto:\\1\">\\1</a>",$string);
	}

	return $string;
}

/**
 * formats the date according to the locale settings
 *
 * @author  Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @author  Christophe Gesché <gesche@ipm.ucl.ac.be>
 *          originally inspired from from PhpMyAdmin
 * @param  string  $formatOfDate date pattern
 * @param  integer $timestamp, default is NOW.
 * @return the formatted date
 */

function format_locale_date( $dateFormat, $timeStamp = -1)
{
	// Defining the shorts for the days
	$DaysShort = array (get_lang("SundayShort"), get_lang("MondayShort"), get_lang("TuesdayShort"), get_lang("WednesdayShort"), get_lang("ThursdayShort"), get_lang("FridayShort"), get_lang("SaturdayShort"));
	// Defining the days of the week to allow translation of the days
	$DaysLong = array (get_lang("SundayLong"), get_lang("MondayLong"), get_lang("TuesdayLong"), get_lang("WednesdayLong"), get_lang("ThursdayLong"), get_lang("FridayLong"), get_lang("SaturdayLong"));
	// Defining the shorts for the months
	$MonthsShort = array (get_lang("JanuaryShort"), get_lang("FebruaryShort"), get_lang("MarchShort"), get_lang("AprilShort"), get_lang("MayShort"), get_lang("JuneShort"), get_lang("JulyShort"), get_lang("AugustShort"), get_lang("SeptemberShort"), get_lang("OctoberShort"), get_lang("NovemberShort"), get_lang("DecemberShort"));
	// Defining the months of the year to allow translation of the months
	$MonthsLong = array (get_lang("JanuaryLong"), get_lang("FebruaryLong"), get_lang("MarchLong"), get_lang("AprilLong"), get_lang("MayLong"), get_lang("JuneLong"), get_lang("JulyLong"), get_lang("AugustLong"), get_lang("SeptemberLong"), get_lang("OctoberLong"), get_lang("NovemberLong"), get_lang("DecemberLong"));

	if ($timeStamp == -1) $timeStamp = time();

	// with the ereg  we  replace %aAbB of date format
	//(they can be done by the system when  locale date aren't aivailable

	$date = ereg_replace('%[A]', $DaysLong[(int)strftime('%w', $timeStamp)], $dateFormat);
	$date = ereg_replace('%[a]', $DaysShort[(int)strftime('%w', $timeStamp)], $date);
	$date = ereg_replace('%[B]', $MonthsLong[(int)strftime('%m', $timeStamp)-1], $date);
	$date = ereg_replace('%[b]', $MonthsShort[(int)strftime('%m', $timeStamp)-1], $date);

	return strftime($date, $timeStamp);

} // end function format_locale_date



?>