<?php // $Id$
/**
==============================================================================
*	This script displays the Dokeos header.
*
*	@package dokeos.include
==============================================================================
*/

/*----------------------------------------
              HEADERS SECTION
  --------------------------------------*/

/*
 * HTTP HEADER
 */

//Give a default value to $charset. Should change to UTF-8 some time in the future.
//This parameter should be set in the platform configuration interface in time.
if(empty($charset))
{
	$charset = 'ISO-8859-15';
}
$charset = 'UTF-8';
//header('Content-Type: text/html; charset='. $charset)
//	or die ("WARNING : it remains some characters before &lt;?php bracket or after ?&gt end");

header('Content-Type: text/html; charset='. $charset);
if ( isset($httpHeadXtra) && $httpHeadXtra )
{
	foreach($httpHeadXtra as $thisHttpHead)
	{
		header($thisHttpHead);
	}
}

// Get language iso-code for this page - ignore errors
// The error ignorance is due to the non compatibility of function_exists()
// with the object syntax of Database::get_language_isocode()
@$document_language = Database::get_language_isocode($language_interface);
if(empty($document_language))
{
  //if there was no valid iso-code, use the english one
  $document_language = 'en';
}

/*
 * HTML HEADER
 */

echo '<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'. $document_language .'" lang="'. $document_language .'">'."\n";
echo '<head>'."\n";
echo '<title>';

if(!empty($nameTools))
{
	echo $nameTools.' - ';
}

echo get_setting('siteName');

echo '</title>'."\n";
echo '<style type="text/css" media="screen, projection">'."\n";
echo '/*<![CDATA[*/'."\n";
echo '@import "'. api_get_path(WEB_CODE_PATH) .'css/default.css";'."\n";
echo '/*]]>*/'."\n";
echo '</style>'."\n";
echo '<style type="text/css" media="print">'."\n";
echo '/*<![CDATA[*/'."\n";
echo '@import "'. api_get_path(WEB_CODE_PATH) .'>css/print.css";'."\n";
echo '/*]]>*/'."\n";
echo '</style>'."\n";

if(api_get_setting('stylesheets')<>'')
{
	echo '<style type="text/css" media="screen, projection">'."\n";
	echo '/*<![CDATA[*/'."\n";
	echo '@import "'. api_get_path(WEB_CODE_PATH). 'css/'. api_get_setting('stylesheets') .'/default.css";'."\n";
	echo '/*]]>*/'."\n";
	echo '</style>'."\n";
}

echo '<link rel="top" href="'. api_get_path(WEB_PATH). 'index.php" title="" />'."\n";
echo '<link rel="courses" href="'. api_get_path(WEB_CODE_PATH). 'auth/courses.php" title="'. htmlentities(get_lang('OtherCourses')). '" />'."\n";
echo '<link rel="profil" href="'. api_get_path(WEB_CODE_PATH). 'auth/profile.php" title="'. htmlentities(get_lang('ModifyProfile')). '" />'."\n";
echo '<link href="http://www.dokeos.com/documentation.php" rel="Help" />'."\n";
echo '<link href="http://www.dokeos.com/team.php" rel="Author" />'."\n";
echo '<link href="http://www.dokeos.com" rel="Copyright" />'."\n";
echo '<link rel="shortcut icon" href="'. api_get_path(WEB_PATH). 'favicon.ico" type="image/x-icon" />'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset='. $charset .'" />'."\n";

if ( isset($htmlHeadXtra) && $htmlHeadXtra )
{
	foreach($htmlHeadXtra as $this_html_head)
	{
		echo($this_html_head);
	}
}

echo '</head>'."\n";
echo '<body dir="'. $text_dir.'"';
if(defined('DOKEOS_HOMEPAGE') && DOKEOS_HOMEPAGE)
{
	echo 'onload="javascript:if(document.formLogin) { document.formLogin.login.focus(); }"';
}
echo ">\n";

echo '<!-- #outerframe container to control some general layout of all pages -->'."\n";
echo '<div id="outerframe">'."\n";

//  Banner
include(api_get_include_path()."/claro_init_banner.inc.php");
?>
