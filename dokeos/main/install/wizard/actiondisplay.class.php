<?php
/**
 * @package main
 * @subpackage install
 */
/**
 * Class to render a page in the install wizard.
 */
class ActionDisplay extends HTML_QuickForm_Action_Display
{
	/**
	 * Displays the HTML-code of a page in the wizard
	 * @param HTML_Quickform_Page $page The page to display.
	 */
	function _renderForm($current_page)
	{
		global $dokeos_version, $installType, $updateFromVersion;
		$renderer = $current_page->defaultRenderer();
		$current_page->setRequiredNote('<font color="#FF0000">*</font> '.get_lang('ThisFieldIsRequired'));
		$element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
		$renderer->setElementTemplate($element_template);
		$header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
		$renderer->setHeaderTemplate($header_template);
		HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>'.get_lang('ThisFieldIsRequired').'</small>');
		$current_page->accept($renderer);
		
		echo '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
		echo '<head>'."\n";
		echo '<title>-- ' .$dokeos_version . ' Installation --</title>'."\n";
		echo '<link rel="stylesheet" href="../css/default.css" type="text/css"/>'."\n";
		echo '<link rel="stylesheet" href="../css/skorpiuz.css" type="text/css"/>'."\n";		
		echo '</head>'."\n";
		echo '<body dir="'. get_lang('text_dir') .'">' . "\n";
		
		echo '<!-- #outerframe container to control some general layout of all pages -->'."\n";
		echo '<div id="outerframe">'."\n";
		
		echo '<div id="header">  <!-- header section start -->'."\n";
		echo '<div id="header1"> <!-- top of banner with institution name/hompage link -->'."\n";
		echo 'Dokeos installation - version ' . $dokeos_version;
		if ($installType == 'new')
		{
			echo ' - New installation';
		}
		elseif ($installType == 'update')
		{
			echo ' - Update from Dokeos ' . implode('|',$updateFromVersion);
		}		
		echo '<div class="clear">&nbsp;</div>'."\n";
		echo '</div> <!-- end of the whole #header section -->'."\n";
		echo '<!--   Begin Of script Output   -->'."\n";
		
		echo '<div style="float: left; background-color:#EFEFEF;margin-right: 20px;padding: 15px;">';
		echo '<img src="../img/bluelogo.gif" alt="logo"/>';
		$all_pages = $current_page->controller->_pages;
		$total_number_of_pages = count($all_pages);
		$current_page_number = 0;
		$page_number = 0;
		echo '<ol>';
		foreach($all_pages as $index => $page)
		{
			$page_number++;
			if($page->get_title() == $current_page->get_title())
			{
				$current_page_number = $page_number;
				echo '<li style="font-weight: bold;">'.$page->get_title().'</li>';
			}
			else
			{
				echo '<li>'.$page->get_title().'</li>';
			}
		}
		echo '</ol>';
		echo '</div>' . "\n";
		
		echo '<div style="margin: 10px;">';
		echo '<h2>'.get_lang('Step').' '.$current_page_number.' '.get_lang('of').' '.$total_number_of_pages.' &ndash; '.$current_page->get_title().'</h2>';
		echo '<div>';
		echo $current_page->get_info();
		echo '</div>';
		echo $renderer->toHtml();
		echo '</div>';
		
		echo '<div class="clear">&nbsp;</div> <!-- \'clearing\' div to make sure that footer stays below the main and right column sections -->'."\n";
		echo "\n";
		echo '<div id="footer"> <!-- start of #footer section -->'."\n";
		echo $dokeos_version . '&nbsp;&copy;&nbsp;2007-'. date('Y'). '&nbsp;Scaramanga Productions';
		echo '</div> <!-- end of #footer -->'."\n";
		echo '</div> <!-- end of #outerframe opened in header -->'."\n";
		echo "\n";
		echo '</body>'."\n";
		echo '</html>'."\n";
	}
}
?>