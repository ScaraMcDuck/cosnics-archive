<?php
class MaintenanceWizardDisplay extends HTML_QuickForm_Action_Display
{
	private $parent;
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
		$this->parent->display_header();
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