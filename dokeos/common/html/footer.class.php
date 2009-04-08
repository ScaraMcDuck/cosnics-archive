<?php
/**
 * $Id$
 * @package repository
 */
/**
 * Class to display the footer of a HTML-page
 */
class Footer
{
	/**
	 * Create a new Footer
	 */
	function Footer()
	{
	}
	
	function get_setting($variable, $application)
	{
		return PlatformSetting :: get($variable, $application);
	}
	
	/**
	 * Display the footer
	 */
	function display()
	{
		echo $this->toHtml();
	}
	/**
	 * Returns the HTML code for the footer
	 */
	function toHtml()
	{
		$output[] = '<div class="clear">&nbsp;</div> <!-- "clearing" div to make sure that footer stays below the main and right column sections -->';
		$output[] = '</div> <!-- end of #main" started at the end of banner.inc.php -->';
		$output[] = '<div id="footer"> <!-- start of #footer section -->';
		
		if (Authentication :: is_valid()) 
		{
			$output[] = '<div id="footer_menu">';
			$output[] = '<div class="categories">';
			$udm = UserDataManager :: get_instance();
			$user = $udm->retrieve_user(Session :: get_user_id());
			
			$menumanager = new MenuManager($user);
			$output[] = $menumanager->render_menu(MenuManager :: ACTION_RENDER_SITEMAP);
			$output[] = '<div class="clear"></div>';
			$output[] = '</div>';
			$output[] = '<div class="clear"></div>';
			$output[] = '</div>';
		}
		
		$output[] = '<div id="copyright">';
		$output[] = '<div class="logo">';
		$output[] = '<a href="http://www.dokeosplanet.org"><img src="'. Theme :: get_common_image_path() .'dokeos_logo_small.png" /></a>';
		$output[] = '</div>';
		$output[] = '<div class="links">';
		$output[] = Translation :: get('License') . '&nbsp;|&nbsp;' . Translation :: get('PrivacyPolicy') . '&nbsp;|&nbsp;' . Translation :: get('Contact') . '&nbsp;|&nbsp;<a href="http://www.dokeosplanet.org">http://www.dokeosplanet.org</a>';
		$output[] = '</div>';
		$output[] = '<div class="clear"></div>';
		$output[] = '</div>';
		
		$output[] = '';
		$output[] = '';
		$output[] = '';
		$output[] = '';
		$output[] = '';
		$output[] = '';
		$output[] = '';
		
//		$output[] = '<a href="http://www.dokeosplanet.org"><img src="'. Theme :: get_common_image_path() .'dokeos_logo_small.png" /></a>';
//		
//		$output[] = '<div class="copyright">';
//		$output[] = Translation :: get('Platform').'&nbsp;<a href="http://www.dokeosplanet.org">'. $this->get_setting('site_name', 'admin') . ' ' . $this->get_setting('version', 'admin').'</a>&nbsp;&copy;&nbsp;'.date('Y');
//		$output[] = '</div>';
//		
//		$admin_data = '';
//		if ($this->get_setting('show_administrator_data', 'admin') == "true")
//		{
//			$admin_data .= Translation :: get('Manager');
//			$admin_data .= ':&nbsp;';
//			$admin_data .= Display :: encrypted_mailto_link($this->get_setting('administrator_email', 'admin'), $this->get_setting('administrator_surname', 'admin').' '.$this->get_setting('administrator_firstname', 'admin'));
//		}
//		$output[] = '    '.$admin_data.'&nbsp;';
		
		$output[] = '   </div> <!-- end of #footer -->';
		$output[] = '  </div> <!-- end of #outerframe opened in header -->';
		$output[] = ' </body>';
		$output[] = '</html>';
		//$output[] = '<script language="JavaScript">( function($) { $(window).unload(function() { alert("ByeNow!"); }); })(jQuery);</script>';
		return implode("\n",$output);
	}
}
?>