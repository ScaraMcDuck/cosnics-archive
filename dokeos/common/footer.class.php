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
	private $admindatamanager;
	private $version;
	/**
	 * Create a new Footer
	 */
	function Footer($admindatamanager, $version)
	{
		$this->admindatamanager = $admindatamanager;
		$this->version = $version;
	}
	
	function get_setting($variable, $application)
	{
		$adm		= $this->admindatamanager;
		$setting	= $adm->retrieve_setting_from_variable_name($variable, $application);
		return $setting->get_value();
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
		$output[] = '   </div> <!-- end of #main" started at the end of claro_init_banner.inc.php -->';
		$output[] = '   <div id="footer"> <!-- start of #footer section -->';
		$output[] = '    <div class="copyright">';
		$output[] = '     '.get_lang('Platform').'&nbsp;<a href="http://www.dokeos.com">'.$this->version.'</a>&nbsp;&copy;&nbsp;'.date('Y');
		$output[] = '    </div>';
		$admin_data = '';
		if ($this->get_setting('show_administrator_data', 'admin') == "true")
		{
			$admin_data .= get_lang('Manager');
			$admin_data .= ':&nbsp;';
			$admin_data .= Display :: encrypted_mailto_link($this->get_setting('administrator_email', 'admin'), $this->get_setting('administrator_surname', 'admin').' '.$this->get_setting('administrator_firstname', 'admin'));
		}
		$output[] = '    '.$admin_data.'&nbsp;';
		$output[] = '   </div> <!-- end of #footer -->';
		$output[] = '  </div> <!-- end of #outerframe opened in header -->';
		$output[] = ' </body>';
		$output[] = '</html>';
		return implode("\n",$output);
	}
}
?>