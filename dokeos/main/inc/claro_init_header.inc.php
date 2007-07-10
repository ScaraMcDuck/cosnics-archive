<?php // $Id$
/**
 *	This script displays the Dokeos header.
 *	@package include
 */
require_once(dirname(__FILE__).'/../../common/header.class.php');

// Get language iso-code for this page - ignore errors
// The error ignorance is due to the non compatibility of function_exists()
// with the object syntax of Database::get_language_isocode()
@$document_language = Database::get_language_isocode($language_interface);
if(empty($document_language))
{
  //if there was no valid iso-code, use the english one
  $document_language = 'en';
}


$header = new Header($document_language);
$header->add_default_headers();
$header->set_page_title((empty($nameTools) ? '' : $nameTools.' - ').get_setting('siteName'));
if ( isset($httpHeadXtra) && $httpHeadXtra )
{
	foreach($httpHeadXtra as $thisHttpHead)
	{
		$header->add_http_header($thisHttpHead);
	}
}

if(api_get_setting('stylesheets')<>'')
{
	$header->add_css_file_header(api_get_path(WEB_CODE_PATH). 'css/'. api_get_setting('stylesheets') .'/default.css');
}
if ( isset($htmlHeadXtra) && $htmlHeadXtra )
{
	foreach($htmlHeadXtra as $this_html_head)
	{
		$header->add_html_header($this_html_head);
	}
}
$header->display();



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
