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
class MaintenanceWizardDisplay extends HTML_QuickForm_Action_Display
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
	public function MaintenanceWizardDisplay($parent)
	{
		$this->parent = $parent;
	}
	/**
	 * Displays the HTML-code of a page in the wizard
	 * @param HTML_Quickform_Page $page The page to display.
	 */
	function _renderForm($current_page)
	{
		$trail = new BreadcrumbTrail();
		
		$this->parent->display_header($trail);
		if(isset($_SESSION['maintenance_message']))
		{
			Display::display_normal_message($_SESSION['maintenance_message']);
			unset($_SESSION['maintenance_message']);
		}
		if(isset($_SESSION['maintenance_error_message']))
		{
			Display::display_error_message($_SESSION['maintenance_error_message']);
			unset($_SESSION['maintenance_error_message']);
		}
		parent::_renderForm($current_page);
		$this->parent->display_footer();
	}
}
?>