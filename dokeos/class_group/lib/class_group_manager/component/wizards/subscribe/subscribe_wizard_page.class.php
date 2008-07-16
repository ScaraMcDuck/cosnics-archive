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
abstract class SubscribeWizardPage extends HTML_QuickForm_Page
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	private $wizard;
	/**
	 * Constructor
	 * @param string $name A unique name of this page in the wizard
	 * @param RepositoryTool $parent The repository tool in which the wizard
	 * runs.
	 */
	public function SubscribeWizardPage($name,$parent,$wizard = null)
	{
		$this->registerElementType('element_finder', Path :: get_library_path().'html/formvalidator/Element/element_finder.php', 'HTML_QuickForm_element_finder');
		$this->parent = $parent;
		$this->wizard = $wizard;
		parent::HTML_QuickForm_Page($name,'post');
		$this->updateAttributes(array('action'=>$parent->get_url(array(ClassGroupManager :: PARAM_CLASSGROUP_ID => $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID]))));
	}
	/**
	 * Returns the repository tool in which this wizard runs
	 * @return RepositoryTool
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	function get_wizard()
	{
		return $this->wizard;
	}
}
?>