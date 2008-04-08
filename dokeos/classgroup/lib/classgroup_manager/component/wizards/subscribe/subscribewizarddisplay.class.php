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
class SubscribeWizardDisplay extends HTML_QuickForm_Action_Display
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
	public function SubscribeWizardDisplay($parent)
	{
		$this->parent = $parent;
	}
	/**
	 * Displays the HTML-code of a page in the wizard
	 * @param HTML_Quickform_Page $page The page to display.
	 */
	function _renderForm($current_page)
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->parent->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), 'name' => Translation :: get('ClassGroups'));
		$breadcrumbs[] = array ('url' => $this->parent->get_url(), 'name' => Translation :: get('SubscribeUsersToGroup'));
		
		$this->parent->display_header($breadcrumbs);
		if(isset($_SESSION['subscribe_message']))
		{
			Display::display_normal_message($_SESSION['subscribe_message']);
			unset($_SESSION['subscribe_message']);
		}
		if(isset($_SESSION['subscribe_error_message']))
		{
			Display::display_error_message($_SESSION['subscribe_error_message']);
			unset($_SESSION['subscribe_error_message']);
		}
		parent::_renderForm($current_page);
		$this->parent->display_footer();
	}
}
?>