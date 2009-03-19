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
		$output[] = '    <div class="clear">&nbsp;</div> <!-- "clearing" div to make sure that footer stays below the main and right column sections -->';
		$output[] = '   </div> <!-- end of #main" started at the end of banner.inc.php -->';
		$output[] = '   <div id="footer"> <!-- start of #footer section -->';
		$output[] = '    <div class="copyright">';
		$output[] = '     '.Translation :: get('Platform').'&nbsp;<a href="http://www.dokeosplanet.org">'. $this->get_setting('site_name', 'admin') . ' ' . $this->get_setting('version', 'admin').'</a>&nbsp;&copy;&nbsp;'.date('Y');
		$output[] = '    </div>';
		$admin_data = '';
		if ($this->get_setting('show_administrator_data', 'admin') == "true")
		{
			$admin_data .= Translation :: get('Manager');
			$admin_data .= ':&nbsp;';
			$admin_data .= Display :: encrypted_mailto_link($this->get_setting('administrator_email', 'admin'), $this->get_setting('administrator_surname', 'admin').' '.$this->get_setting('administrator_firstname', 'admin'));
		}
		$output[] = '    '.$admin_data.'&nbsp;';
		$output[] = '   </div> <!-- end of #footer -->';
		$output[] = '  </div> <!-- end of #outerframe opened in header -->';
		$output[] = ' </body>';
		$output[] = '</html>';
		//$output[] = '<script language="JavaScript">( function($) { $(window).unload(function() { alert("ByeNow!"); }); })(jQuery);</script>';
		return implode("\n",$output);
	}
}
?>