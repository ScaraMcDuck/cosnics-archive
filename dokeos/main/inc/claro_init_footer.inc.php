<?php
/**
============================================================================== 
*	This script displays the footer that is below (almost)
*	every Dokeos web page.
*
*	@package dokeos.include
============================================================================== 
*/

echo '<div class="clear">&nbsp;</div> <!-- \'clearing\' div to make sure that footer stays below the main and right column sections -->'."\n";
echo '</div> <!-- end of #main" started at the end of claro_init_banner.inc.php -->'."\n";
echo "\n"."\n";
echo '<div id="footer"> <!-- start of #footer section -->'."\n";
echo '<div class="copyright">'."\n";
echo get_lang('Platform'). '&nbsp;<a href="http://www.dokeos.com">'. $dokeos_version . '</a>&nbsp;&copy;&nbsp;' .date('Y')."\n";
echo '</div>'."\n";
if (get_setting('show_administrator_data')=="true")
{
  	echo get_lang('Manager');
  	echo '&nbsp;:&nbsp;';
  	echo Display::encrypted_mailto_link(get_setting('emailAdministrator'),get_setting('administratorSurname').' '.get_setting('administratorName')); 
}
echo '&nbsp;';
echo '</div> <!-- end of #footer -->'."\n";
echo "\n";
echo '</div> <!-- end of #outerframe opened in header -->'."\n";
echo "\n";
echo '</body>'."\n";
echo '</html>'."\n";

?>