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
		print_r($values);
		$page->controller->container(true);
		$this->parent->display_footer();
	}
}
?>