<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class InstallWizardDisplay extends HTML_QuickForm_Action_Display
{
	/**
	 * The repository tool in which the wizard runs
	 */
	private $parent;
	/**
	 * Constructor
	 * @param RepositoryTool $parent The repository tool in which the wizard
	 * runs
	 */
	public function InstallWizardDisplay($parent)
	{
		$this->parent = $parent;
	}
	/**
	 * Displays the HTML-code of a page in the wizard
	 * @param HTML_Quickform_Page $page The page to display.
	 */
	function _renderForm($current_page)
	{
		$renderer = & $current_page->defaultRenderer();
		$current_page->setRequiredNote('<font color="#FF0000">*</font> '.get_lang('ThisFieldIsRequired'));
		$element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
		$renderer->setElementTemplate($element_template);
		$header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
		$renderer->setHeaderTemplate($header_template);
		HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>'.get_lang('ThisFieldIsRequired').'</small>');
		$current_page->accept($renderer);
		
		$this->parent->display_header();
		
		echo '<div style="float: left; background-color:#EFEFEF;margin-right: 20px;padding: 15px;">';
		echo '<img src="../main/img/bluelogo.gif" alt="logo"/>';
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
		
		if(isset($_SESSION['install_message']))
		{
			Display::display_normal_message($_SESSION['install_message']);
			unset($_SESSION['install_message']);
		}
		if(isset($_SESSION['install_error_message']))
		{
			Display::display_error_message($_SESSION['install_error_message']);
			unset($_SESSION['install_error_message']);
		}
		
		parent::_renderForm($current_page);
		echo '</div>';
		
		$this->parent->display_footer();
	}
}
?>