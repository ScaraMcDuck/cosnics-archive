<?php // $Id$
/**
 *	This script displays the Dokeos header.
 *	@package include
 */
require_once(dirname(__FILE__).'/../../common/header.class.php');
require_once(dirname(__FILE__).'/../../common/banner.class.php');

// Get language iso-code for this page - ignore errors
// The error ignorance is due to the non compatibility of function_exists()
// with the object syntax of Database::get_language_isocode()

$document_language = $adm->retrieve_language_from_english_name($language_interface)->get_isocode();
if(empty($document_language))
{
  //if there was no valid iso-code, use the english one
  $document_language = 'en';
}


$header = new Header($document_language);
$header->add_default_headers();
$header->set_page_title((empty($nameTools) ? '' : $nameTools.' - ').$adm->retrieve_setting_from_variable_name('site_name', 'admin')->get_value());
if ( isset($httpHeadXtra) && $httpHeadXtra )
{
	foreach($httpHeadXtra as $thisHttpHead)
	{
		$header->add_http_header($thisHttpHead);
	}
}

if($adm->retrieve_setting_from_variable_name('stylesheets', 'admin')->get_value()<>'')
{
	$header->add_css_file_header(Path :: get_path(WEB_CSS_PATH) . $adm->retrieve_setting_from_variable_name('stylesheets', 'admin')->get_value() .'/default.css');
}
if ( isset($htmlHeadXtra) && $htmlHeadXtra )
{
	foreach($htmlHeadXtra as $this_html_head)
	{
		$header->add_html_header($this_html_head);
	}
}
$header->display();

if(!isset($text_dir))
{
	$text_dir = 'ltr';
}

echo '<body dir="'. $text_dir.'"';
if(defined('DOKEOS_HOMEPAGE') && DOKEOS_HOMEPAGE)
{
	echo 'onload="javascript:if(document.formLogin) { document.formLogin.login.focus(); }"';
}
echo ">\n";

echo '<!-- #outerframe container to control some general layout of all pages -->'."\n";
echo '<div id="outerframe">'."\n";

//  Banner
$banner = new Banner($adm);
$banner->display();
?>