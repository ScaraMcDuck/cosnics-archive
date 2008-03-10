<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
/**
 * This class provides the needed functionality to show a page in a migration
 * wizard.
 * 
 * @author Sven Vanpoucke
 */
class MigrationWizardDisplay extends HTML_QuickForm_Action_Display
{
	/**
	 * The Migration Wizard Component in which the wizard runs
	 */
	private $parent;
	/**
	 * Constructor
	 * @param RepositoryTool $parent The repository tool in which the wizard
	 * runs
	 */
	public function MigrationWizardDisplay($parent)
	{
		$this->parent = $parent;
	}
	/**
	 * Displays the HTML-code of a page in the wizard
	 * @param HTML_Quickform_Page $page The page to display.
	 */
	function _renderForm($current_page)
	{
		$renderer = $current_page->defaultRenderer();
		$current_page->setRequiredNote('<font color="#FF0000">*</font> '.Translation :: get_lang('ThisFieldIsRequired'));
		$element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
		$renderer->setElementTemplate($element_template);
		$header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
		$renderer->setHeaderTemplate($header_template);
		HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>'.Translation :: get_lang('ThisFieldIsRequired').'</small>');
		$current_page->accept($renderer);
		
		$this->parent->display_header();
		
		echo '<div style="float: left; background-color:#EFEFEF;margin-right: 20px;padding: 15px;">';
		echo '<img src="../layout/img/bluelogo.gif" alt="logo"/>';
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
		echo '<h2>'.Translation :: get_lang('Step').' '.$current_page_number.' '.Translation :: get_lang('of').' '.$total_number_of_pages.' &ndash; '.$current_page->get_title().'</h2>';
		echo '<div>';
		$performed_correct = $current_page->perform();
		if($performed_correct)
			echo $current_page->get_info();
		echo '</div>';
		
		parent::_renderForm($current_page);
		
		echo '</div>';
		
		echo $current_page->next_step_info();
		
		$this->parent->display_footer();
	}
}
?>
