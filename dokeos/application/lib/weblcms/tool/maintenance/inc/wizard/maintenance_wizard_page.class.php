<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
/**
 * This abstract class defines a page which is used in a maintenance wizard.
 */
abstract class MaintenanceWizardPage extends HTML_QuickForm_Page
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	/**
	 * Constructor
	 * @param string $name A unique name of this page in the wizard
	 * @param Tool $parent The repository tool in which the wizard
	 * runs.
	 */
	public function MaintenanceWizardPage($name,$parent)
	{
		$this->parent = $parent;
		parent::HTML_QuickForm_Page($name,'post');
		$this->updateAttributes(array('action'=>$parent->get_url()));
	}
	/**
	 * Returns the repository tool in which this wizard runs
	 * @return Tool
	 */
	function get_parent()
	{
		return $this->parent;
	}
}
?>