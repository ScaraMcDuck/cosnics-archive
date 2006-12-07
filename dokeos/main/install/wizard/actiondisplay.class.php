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
	function _renderForm(& $page)
	{
		global $dokeos_version, $installType, $updateFromVersion;
		$renderer = & $page->defaultRenderer();
		$page->setRequiredNote('<font color="#FF0000">*</font> '.get_lang('ThisFieldIsRequired'));
		$element_template = "\n\t<tr>\n\t\t<td align=\"right\" valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
		$renderer->setElementTemplate($element_template);
		$header_template = "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
		$renderer->setHeaderTemplate($header_template);
		HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>'.get_lang('ThisFieldIsRequired').'</small>');
		$page->accept($renderer);
?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
		<title>-- Dokeos installation -- version <?php echo $dokeos_version; ?></title>
		<link rel="stylesheet" href="../css/default.css" type="text/css"/>
		</head>
		<body dir="<?php echo get_lang('text_dir'); ?>">
		<div style="background-color:#4171B5;color:white;font-size:x-large;">
			Dokeos installation - version <?php echo $dokeos_version; ?><?php if($installType == 'new') echo ' - New installation'; else if($installType == 'update') echo ' - Update from Dokeos '.implode('|',$updateFromVersion); ?>
		</div>
		<div style="margin:50px;">
			<img src="../img/bluelogo.gif" alt="logo" align="right"/>
			<?php


		echo '<h2>'.$page->get_title().'</h2>';
		echo '<p>';
		echo $page->get_info();
		echo '</p>';
		echo $renderer->toHtml();
		?>
        </div>
		</body>
		</html>
		<?php
	}
}
?>