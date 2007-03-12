<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
class MaintenanceWizardProcess extends HTML_QuickForm_Action
{
	private $parent;
	public function MaintenanceWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	function perform(& $page, $actionName)
	{
		$this->parent->display_header();
		$values = $page->controller->exportValues();
		echo '<strong>TODO</strong>';
		switch($values['action'])
		{
			case ActionSelectionMaintenanceWizardPage::ACTION_EMPTY:
				echo 'EMPTY';
				break;
			case ActionSelectionMaintenanceWizardPage::ACTION_COPY:
				echo 'COPY';
				break;
			case ActionSelectionMaintenanceWizardPage::ACTION_BACKUP:
				echo 'BACKUP';
				break;
			case ActionSelectionMaintenanceWizardPage::ACTION_DELETE:
				echo 'DELETE';
				break;
		}
		print_r($values);
		$page->controller->container(true);
		$this->parent->display_footer();
	}
}
?>