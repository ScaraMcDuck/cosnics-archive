<?php

// $Id: display.lib.php 14299 2008-02-15 10:55:33Z Scara84 $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Roan Embrechts, Vrije Universiteit Brussel
	Copyright (c) Wolfgang Schneider
	Copyright (c) Bert Vanderkimpen, Ghent University
	Copyright (c) Bart Mollet, Hogeschool Gent
	Copyright (c) Rene Haentjens, Ghent University
	Copyright (c) Yannick Warnier, Dokeos S.A.
	Copyright (c) Sandra Matthys, Hogeschool Gent
	Copyright (c) Denes Nagy, Dokeos S.A.

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
*	This is a display library for Dokeos.
*
*	Include/require it in your code to use its functionality.
*	There are also several display functions in the main api library.
*
*	All functions static functions inside a class called Display,
*	so you use them like this: e.g.
*	Display :: normal_message($message)
*
*	@package dokeos.library
==============================================================================
*/
/*
==============================================================================
	   CONSTANTS
==============================================================================
*/
/** the light grey often used in Dokeos*/
define("DOKEOSLIGHTGREY", "#E6E6E6");
/** plain white colour*/
define("HTML_WHITE", "white");
/**
*	Display class
*	contains several functions dealing with the display of
*	table data, messages, help topics, ...
*
*	@version 1.0.4
*	@package dokeos.library
*/
require_once(dirname(__FILE__).'/table/sortable_table.class.php');
require_once(dirname(__FILE__).'/footer.class.php');

class Display
{
	/**
	* Displays a normal message. It is recommended to use this function
	* to display any normal information messages.
	*
	* @author Roan Embrechts
	* @author Tim De Pauw
	* @param string $message - include any additional html
	*                          tags if you need them
	* @param boolean $return
	* @return mixed
	*/
	public static function normal_message($message, $return = false)
	{
		$out = '';
		if (!headers_sent())
		{
			$out .= '<style type="text/css" media="screen, projection">
/*<![CDATA[*/
@import "' . Path :: get(WEB_CSS_PATH) . 'default.css";
/*]]>*/
</style>';
		}
		$out .= '<div class="normal-message">'.$message.'</div>';
		if ($return) {
			return $out;
		}
		echo $out;
	}

	/**
	* Displays a message. It is recommended to use this function
	* to display any confirmation or error messages.
	*
	* @author Hugues Peeters
	* @author Roan Embrechts
	* @author Tim De Pauw
	* @param string $message - include any additional html
	*                          tags if you need them
	* @param boolean $return
	* @return mixed
	*/
	public static function error_message($message, $return = false)
	{
		$out = '';
		if (!headers_sent())
		{
			$out .= '<style type="text/css" media="screen, projection">
/*<![CDATA[*/
@import "' . Path :: get(WEB_CSS_PATH) . 'default.css";
/*]]>*/
</style>';
		}
		$out .= '<div class="error-message">'.$message.'</div>';
		if ($return) {
			return $out;
		}
		echo $out;
	}

	/**
	* Displays a message. It is recommended to use this function
	* to display any warning messages.
	*
	* @author Hugues Peeters
	* @author Roan Embrechts
	* @author Tim De Pauw
	* @author Hans De Bisschop
	* @param string $message - include any additional html
	*                          tags if you need them
	* @param boolean $return
	* @return mixed
	*/
	public static function warning_message($message, $return = false)
	{
		$out = '';
		if (!headers_sent())
		{
			$out .= '<style type="text/css" media="screen, projection">
/*<![CDATA[*/
@import "' . Path :: get(WEB_CSS_PATH) . 'default.css";
/*]]>*/
</style>';
		}
		$out .= '<div class="warning-message">'.$message.'</div>';
		if ($return) {
			return $out;
		}
		echo $out;
	}
	/**
	 * Return an encrypted mailto hyperlink
	 *
	 * @param - $email (string) - e-mail
	 * @param - $text (string) - clickable text
	 * @param - $style_class (string) - optional, class from stylesheet
	 * @return - encrypted mailto hyperlink
	 */
	public static function encrypted_mailto_link($email, $clickable_text = null, $style_class = '')
	{
		if (is_null($clickable_text))
		{
			$clickable_text = $email;
		}
		//mailto already present?
		if (substr($email, 0, 7) != 'mailto:')
			$email = 'mailto:'.$email;

		//class (stylesheet) defined?
		if ($style_class != '')
		{
			$style_class = ' class="full_url_print '.$style_class.'"';
		}
		else
		{
			$style_class = ' class="full_url_print"';
		}

		//encrypt email
		$hmail = '';
		for ($i = 0; $i < strlen($email); $i ++)
			$hmail .= '&#'.ord($email {
			$i }).';';

		//encrypt clickable text if @ is present
		if (strpos($clickable_text, '@'))
		{
			for ($i = 0; $i < strlen($clickable_text); $i ++)
				$hclickable_text .= '&#'.ord($clickable_text {
				$i }).';';
		}
		else
		{
			$hclickable_text = htmlspecialchars($clickable_text);
		}

		//return encrypted mailto hyperlink
		return '<a href="'.$hmail.'"'.$style_class.'>'.$hclickable_text.'</a>';
	}

	/**
	 * Display the page header
	 * @param string $tool_name The name of the page (will be showed in the
	 * page title)
	 * @param string $help
	 */
	public static function header($breadcrumbtrail, $help = NULL)
	{
		global $language_interface, $adm, $httpHeadXtra, $htmlHeadXtra, $text_dir, $plugins, $interbreadcrumb, $charset, $noPHP_SELF;
		include (Path :: get(SYS_LIB_PATH).'html/header.inc.php');
	}
	/**
	 * Display the page footer
	 */
	public static function footer()
	{
		global $adm; //necessary to have the value accessible in the footer
		$footer = new Footer($adm);
		$footer->display();
	}
	
	public static function not_allowed($trail = null)
	{
		if (is_null($trail))
		{
			$trail = new BreadcrumbTrail();
		}
		self :: header($trail);
		$home_url = Path :: get(WEB_PATH);
		self :: error_message("<p>Either you are not allowed here or your session has expired.<br><br>You may try <a href=\"$home_url\" target=\"_top\">reconnecting on the home page</a>.</p>");
		$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
		self :: footer();
		exit;
	}
	
	public static function tool_title($titleElement)
	{
		if (is_string($titleElement))
		{
			$tit = $titleElement;
			unset ($titleElement);
			$titleElement['mainTitle'] = $tit;
		}
		echo '<h3>';
		if ($titleElement['supraTitle'])
		{
			echo '<small>'.$titleElement['supraTitle'].'</small><br>';
		}
		if ($titleElement['mainTitle'])
		{
			echo $titleElement['mainTitle'];
		}
		if ($titleElement['subTitle'])
		{
			echo '<br><small>'.$titleElement['subTitle'].'</small>';
		}
		echo '</h3>';
	}
}
?>