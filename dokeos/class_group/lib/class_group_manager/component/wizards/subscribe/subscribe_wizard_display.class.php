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
 require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
 
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
		$trail = new BreadcrumbTrail();
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->parent->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('ClassGroupList')));
		
		$classgroup_id = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		
		if(isset($classgroup_id))
		{
			$classgroup = $this->parent->retrieve_classgroup($classgroup_id);
			$trail->add(new Breadcrumb($this->parent->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $classgroup_id)), $classgroup->get_name()));
		}
		
		$trail->add(new Breadcrumb($this->parent->get_url(), Translation :: get('SubscribeUsersToGroup')));
		
		$this->parent->display_header($trail);
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