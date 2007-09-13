<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	
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
*	This file is a code template; 
*	copy the code and paste it in a new file to begin your own work.
*
*	@package dokeos.plugin
==============================================================================
*/
	
/*
==============================================================================
		INIT SECTION
==============================================================================
*/

// name of the lang file which needs to be included 
//'inc.php' is automatically appended to the file name 
//api_use_lang_files("langFile"); 

// global settings initialisation 
@include("../inc/claro_init_global.inc.php"); 

$nameTools = "Parsing Tester"; // title of the page (comes from the language file) 

Display::display_header($nameTools);

include (api_get_library_path().'/fileUpload.lib.php');

/*
====================================== 
	   Constants & variables
======================================
*/ 
	$example_string = ' Examples
		Simple image 
		<img src = "img/simple.jpg"> 
		Image with extra tag
		<img alt="whatyouseeiswhatyouget" src = "img/extra.jpg">
		Image with lots of whitespace
		<   img     src    = "img/whitespace.jpg">
		Simple hyperlink
		<a href="test.html">link</a>
		More options hyperlink
		<a style=something href="archive/test.html">link</a>
		'				
		;
/*
====================================== 
	   Functions
======================================
*/ 

function display_form($example_string, $text_to_parse)
{
	echo "<form name='hoofdformulier' method='POST' action=\"".$_SERVER['PHP_SELF']."?\"";
	echo "<p>Enter your text to parse here (it will be addslashed and stripslashed): </p>";
	if (isset($_POST["text_entered"]) && $_POST["text_entered"])
	{
		echo "<textarea rows=\"15\" cols=\"80\" name='text_to_parse'>".$text_to_parse."</textarea>";	
	}
	else
	{
		echo "<textarea rows=\"15\" cols=\"80\" name='text_to_parse'>" 
			. $example_string . "</textarea>";
	}	
	echo "<input type='hidden' name='text_entered' value='true'>";
	echo "<input type='submit' value='Parse it'>";
	echo "</form>";
}

/*
====================================== 
	   MAIN CODE
======================================
*/
	
echo "<h1><center>Parsing tester</center></h1>";
echo "<p><center>Tries to fix html tags with href and src parameters.</center></p>";

if (isset($_POST["text_entered"]) && $_POST["text_entered"])
{
	$_POST["text_to_parse"] = stripslashes($_POST["text_to_parse"]);
	$text_to_parse = $_POST["text_to_parse"];
	//Display :: display_normal_message("<b>String to parse:</b><br>" . htmlentities($text_to_parse));
	$result = api_replace_parameter("/examplepath/", $text_to_parse, "src");
	echo "<hr>";
	$result = api_replace_parameter("/examplepath/", $result, "href");
	echo "<hr>";
	Display :: display_normal_message("<b>String result after parsing:</b><br>". htmlentities($result));
}

display_form($example_string, $text_to_parse);	
	
/*
==============================================================================
		FOOTER 
==============================================================================
*/

@Display::display_footer();
?>