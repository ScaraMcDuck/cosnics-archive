<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
 
/**
 * This abstract class defines a page which is used in a archive trackers wizard.
 * @author Sven Vanpoucke
 */
abstract class ArchiveWizardPage extends HTML_QuickForm_Page
{
	/**
	 * The Component which the wizard runs.
	 */
	private $parent;
	/**
	 * Constructor
	 * @param string $name A unique name of this page in the wizard
	 * @param TrackingManagerArchiveComponent $parent The component in which the wizard runs
	 */
	public function ArchiveWizardPage($name,$parent)
	{
		$this->parent = $parent;
		parent::HTML_QuickForm_Page($name,'post');
	}
	/**
	 * Returns the Component in which this wizard runs
	 * @return TrackingManagerArchiveComponent
	 */
	function get_parent()
	{
		return $this->parent;
	}
}
?>