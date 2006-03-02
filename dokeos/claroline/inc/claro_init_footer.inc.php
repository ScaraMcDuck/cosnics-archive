<?php
/**
============================================================================== 
*	This script displays the footer that is below (almost)
*	every Dokeos web page.
*
*	@package dokeos.include
============================================================================== 
*/

/**** display of tool_navigation_menu according to admin setting *****/
if(api_get_setting("show_navigation_menu") == "true")
{

   $course_id = api_get_course_id();
   if ( isset($course_id) )
   {
    echo '</div> <!-- end #center -->';
    echo '</div> <!-- end #centerwrap -->';

      echo '<div id="toolnav"> <!-- start of #toolnav -->';
      require_once(api_get_include_path()."/tool_navigation_menu.inc.php");
      show_navigation_menu();
      echo '</div> <!-- end "#toolnav" -->';
   }    
}  
/***********************************************************************/

?>
 <div class="clear">&nbsp;</div> <!-- 'clearing' div to make sure that footer stays below the main and right column sections -->
</div> <!-- end of #main" started at the end of claro_init_banner.inc.php -->

<div id="footer"> <!-- start of #footer section -->
 <div class="copyright">
  <?php echo get_lang("Platform") ?> <a href="http://www.dokeos.com">Dokeos <?php echo $dokeos_version; ?></a> &copy; <?php echo date('Y'); ?>
 </div>

  <?php 
  if (get_setting('show_administrator_data')=="true")
  	{
  	echo get_lang("Manager") ?> : <?php echo Display::encrypted_mailto_link(get_setting('emailAdministrator'),get_setting('administratorSurname')." ".get_setting('administratorName')); 
	}
  ?>&nbsp;
</div> <!-- end of #footer -->

</div> <!-- end of #outerframe opened in header -->

</body>
</html>