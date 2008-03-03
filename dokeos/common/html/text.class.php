<?php // $Id: text.class.php 7893 2006-03-02 09:54:40Z  $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors
	Copyright (c) 2008 Hans De Bisschop

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

require_once(Path :: get_path(SYS_LIB_PATH) . 'translation/translation.class.php');

class Text
{
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
	public static function make_clickable($string)
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
	 * @author  Christophe Gesch� <gesche@ipm.ucl.ac.be>
	 *          originally inspired from from PhpMyAdmin
	 * @param  string  $formatOfDate date pattern
	 * @param  integer $timestamp, default is NOW.
	 * @return the formatted date
	 */
	public static function format_locale_date( $dateFormat, $timeStamp = -1)
	{
		// Defining the shorts for the days
		$DaysShort = array (Translation :: get_lang("SundayShort"), Translation :: get_lang("MondayShort"), Translation :: get_lang("TuesdayShort"), Translation :: get_lang("WednesdayShort"), Translation :: get_lang("ThursdayShort"), Translation :: get_lang("FridayShort"), Translation :: get_lang("SaturdayShort"));
		// Defining the days of the week to allow translation of the days
		$DaysLong = array (Translation :: get_lang("SundayLong"), Translation :: get_lang("MondayLong"), Translation :: get_lang("TuesdayLong"), Translation :: get_lang("WednesdayLong"), Translation :: get_lang("ThursdayLong"), Translation :: get_lang("FridayLong"), Translation :: get_lang("SaturdayLong"));
		// Defining the shorts for the months
		$MonthsShort = array (Translation :: get_lang("JanuaryShort"), Translation :: get_lang("FebruaryShort"), Translation :: get_lang("MarchShort"), Translation :: get_lang("AprilShort"), Translation :: get_lang("MayShort"), Translation :: get_lang("JuneShort"), Translation :: get_lang("JulyShort"), Translation :: get_lang("AugustShort"), Translation :: get_lang("SeptemberShort"), Translation :: get_lang("OctoberShort"), Translation :: get_lang("NovemberShort"), Translation :: get_lang("DecemberShort"));
		// Defining the months of the year to allow translation of the months
		$MonthsLong = array (Translation :: get_lang("JanuaryLong"), Translation :: get_lang("FebruaryLong"), Translation :: get_lang("MarchLong"), Translation :: get_lang("AprilLong"), Translation :: get_lang("MayLong"), Translation :: get_lang("JuneLong"), Translation :: get_lang("JulyLong"), Translation :: get_lang("AugustLong"), Translation :: get_lang("SeptemberLong"), Translation :: get_lang("OctoberLong"), Translation :: get_lang("NovemberLong"), Translation :: get_lang("DecemberLong"));
	
		if ($timeStamp == -1) $timeStamp = time();
	
		// with the ereg  we  replace %aAbB of date format
		//(they can be done by the system when  locale date aren't aivailable
	
		$date = ereg_replace('%[A]', $DaysLong[(int)strftime('%w', $timeStamp)], $dateFormat);
		$date = ereg_replace('%[a]', $DaysShort[(int)strftime('%w', $timeStamp)], $date);
		$date = ereg_replace('%[B]', $MonthsLong[(int)strftime('%m', $timeStamp)-1], $date);
		$date = ereg_replace('%[b]', $MonthsShort[(int)strftime('%m', $timeStamp)-1], $date);
	
		return strftime($date, $timeStamp);
	
	}
	
	/**
	 * Apply parsing to content to parse tex commandos that are seperated by [tex]
	 * [/tex] to make it readable for techexplorer plugin.
	 * @param string $text The text to parse
	 * @return string The text after parsing.
	 * @author Patrick Cool <patrick.cool@UGent.be>
	 * @version June 2004
	 */
	public static function parse_tex($textext)
	{
		if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
		{
			$textext = str_replace(array ("[tex]", "[/tex]"), array ("<object classid=\"clsid:5AFAB315-AD87-11D3-98BB-002035EFB1A4\"><param name=\"autosize\" value=\"true\" /><param name=\"DataType\" value=\"0\" /><param name=\"Data\" value=\"", "\" /></object>"), $textext);
		}
		else
		{
			$textext = str_replace(array ("[tex]", "[/tex]"), array ("<embed type=\"application/x-techexplorer\" texdata=\"", "\" autosize=\"true\" pluginspage=\"http://www.integretechpub.com/techexplorer/\">"), $textext);
		}
		return $textext;
	}
	
	public static function generate_password($length = 8)
	{
		$characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		if ($length < 2)
		{
			$length = 2;
		}
		$password = '';
		for ($i = 0; $i < $length; $i ++)
		{
			$password .= $characters[rand() % strlen($characters)];
		}
		return $password;
	}
}
?>