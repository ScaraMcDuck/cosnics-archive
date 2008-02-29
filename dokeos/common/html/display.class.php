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
*	Display::display_normal_message($message)
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
require_once 'table/sortabletable.class.php';
require_once(dirname(__FILE__).'/../footer.class.php');
class Display
{
	/**
	*	Display html header of table with several options.
	*
	*	@param $properties, array with elements, all of which have defaults
	*	"width" - the table width, e.g. "100%", default is 85%
	*	"class"	 - the class to use for the table, e.g. "class=\"data_table\""
	*   "cellpadding"  - the extra border in each cell, e.g. "8",default is 4
	*   "cellspacing"  - the extra space between cells, default = 0
	*
	*	@param column_header, array with the header elements.
	*	@author Roan Embrechts
	*	@version 1.01
	*/
	function display_complex_table_header($properties, $column_header)
	{
		$width = $properties["width"];
		if (!isset ($width))
			$width = "85%";
		$class = $properties["class"];
		if (!isset ($class))
			$class = "class=\"data_table\"";
		$cellpadding = $properties["cellpadding"];
		if (!isset ($cellpadding))
			$cellpadding = "4";
		$cellspacing = $properties["cellspacing"];
		if (!isset ($cellspacing))
			$cellspacing = "0";
		//... add more properties as you see fit
		$bgcolor = "bgcolor='".DOKEOSLIGHTGREY."'";
		echo "<table $class border=\"0\" cellspacing=\"$cellspacing\" cellpadding=\"$cellpadding\" width=\"$width\">\n";
		echo "<thead><tr $bgcolor>";
		foreach ($column_header as $table_element)
		{
			echo "<th>".$table_element."</th>";
		}
		echo "</tr></thead>\n";
		echo "<tbody>\n";
		$bgcolor = "bgcolor='".HTML_WHITE."'";
		return $bgcolor;
	}

	/**
	*	Displays a table row.
	*
	*	@param $bgcolor the background colour for the table
	*	@param $table_row an array with the row elements
	*	@param $is_alternating true: the row colours alternate, false otherwise
	*/
	function display_table_row($bgcolor, $table_row, $is_alternating = true)
	{
		echo "<tr $bgcolor>";
		foreach ($table_row as $table_element)
		{
			echo "<td>".$table_element."</td>";
		}
		echo "</tr>\n";
		if ($is_alternating)
		{
			if ($bgcolor == "bgcolor='".HTML_WHITE."'")
				$bgcolor = "bgcolor='".DOKEOSLIGHTGREY."'";
			else
				if ($bgcolor == "bgcolor='".DOKEOSLIGHTGREY."'")
					$bgcolor = "bgcolor='".HTML_WHITE."'";
		}
		return $bgcolor;
	}

	/**
	*	Displays a table row.
	*	This function has more options and is easier to extend than display_table_row()
	*
	*	@param $properties, array with properties:
	*	["bgcolor"] - the background colour for the table
	*	["is_alternating"] - true: the row colours alternate, false otherwise
	*	["align_row"] - an array with, per cell, left|center|right
	*	@todo add valign property
	*/
	function display_complex_table_row($properties, $table_row)
	{
		$bgcolor = $properties["bgcolor"];
		$is_alternating = $properties["is_alternating"];
		$align_row = $properties["align_row"];
		echo "<tr $bgcolor>";
		$number_cells = count($table_row);
		for ($i = 0; $i < $number_cells; $i ++)
		{
			$cell_data = $table_row[$i];
			$cell_align = $align_row[$i];
			echo "<td align=\"$cell_align\">".$cell_data."</td>";
		}
		echo "</tr>\n";
		if ($is_alternating)
		{
			if ($bgcolor == "bgcolor='".HTML_WHITE."'")
				$bgcolor = "bgcolor='".DOKEOSLIGHTGREY."'";
			else
				if ($bgcolor == "bgcolor='".DOKEOSLIGHTGREY."'")
					$bgcolor = "bgcolor='".HTML_WHITE."'";
		}
		return $bgcolor;
	}

	/**
	*	display html footer of table
	*/
	function display_table_footer()
	{
		echo "</tbody></table>";
	}

	/**
	 * Display a table
	 * @param array $header Titles for the table header
	 * 						each item in this array can contain 3 values
	 * 						- 1st element: the column title
	 * 						- 2nd element: true or false (column sortable?)
	 * 						- 3th element: additional attributes for
	 *  						th-tag (eg for column-width)
	 * 						- 4the element: additional attributes for the td-tags
	 * @param array $content 2D-array with the tables content
	 * @param array $sorting_options Keys are:
	 * 					'column' = The column to use as sort-key
	 * 					'direction' = SORT_ASC or SORT_DESC
	 * @param array $paging_options Keys are:
	 * 					'per_page_default' = items per page when switching from
	 * 										 full-	list to per-page-view
	 * 					'per_page' = number of items to show per page
	 * 					'page_nr' = The page to display
	 * @param array $query_vars Additional variables to add in the query-string
	 * @author digitaal-leren@hogent.be
	 */
	function display_sortable_table($header, $content, $sorting_options = array (), $paging_options = array (), $query_vars = null)
	{
		global $origin;
		$column = isset ($sorting_options['column']) ? $sorting_options['column'] : 0;
		$default_items_per_page = isset ($paging_options['per_page']) ? $paging_options['per_page'] : 20;
		$table = new SortableTableFromArray($content, $column, $default_items_per_page);
		if (is_array($query_vars))
		{
			$table->set_additional_parameters($query_vars);
		}
		foreach ($header as $index => $header_item)
		{
			$table->set_header($index, $header_item[0], $header_item[1], $header_item[2], $header_item[3]);
		}
		$table->display();
	}

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
	function display_normal_message($message, $return = false)
	{
		$out = '';
		if (!headers_sent())
		{
			$out .= '<style type="text/css" media="screen, projection">
/*<![CDATA[*/
@import "' . api_get_path(WEB_CODE_PATH) . 'css/default.css";
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
	function display_error_message($message, $return = false)
	{
		$out = '';
		if (!headers_sent())
		{
			$out .= '<style type="text/css" media="screen, projection">
/*<![CDATA[*/
@import "' . api_get_path(WEB_CODE_PATH) . 'css/default.css";
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
	function display_warning_message($message, $return = false)
	{
		$out = '';
		if (!headers_sent())
		{
			$out .= '<style type="text/css" media="screen, projection">
/*<![CDATA[*/
@import "' . api_get_path(WEB_CODE_PATH) . 'css/default.css";
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
	function encrypted_mailto_link($email, $clickable_text = null, $style_class = '')
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
	function display_header($tool_name, $help = NULL)
	{
		$nameTools = $tool_name;
		global $language_interface, $adm, $httpHeadXtra, $htmlHeadXtra, $_course, $_user, $clarolineRepositoryWeb, $text_dir, $plugins, $_uid, $rootAdminWeb, $_cid, $interbredcrump, $charset, $noPHP_SELF;
		include (api_get_include_path()."/header.inc.php");
	}
	/**
	 * Display the page footer
	 */
	function display_footer()
	{
		global $adm, $dokeos_version; //necessary to have the value accessible in the footer
		$footer = new Footer($adm, $dokeos_version);
		$footer->display();
	}

	/**
	 * Print an <option>-list with all letters (A-Z).
	 * @param char $selected_letter The letter that should be selected
	 */
	function get_alphabet_options($selected_letter = '')
	{
		$result = '';
		for ($i = 65; $i <= 90; $i ++)
		{
			$letter = chr($i);
			$result .= '<option value="'.$letter.'"';
			if ($selected_letter == $letter)
			{
				$result .= ' selected="selected"';
			}
			$result .= '>'.$letter.'</option>';
		}
		return $result;
	}

} //end class Display
?>