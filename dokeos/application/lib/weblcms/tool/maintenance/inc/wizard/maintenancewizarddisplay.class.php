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
		parent::_renderForm($current_page);
		$this->parent->display_footer();
	}
}
?>