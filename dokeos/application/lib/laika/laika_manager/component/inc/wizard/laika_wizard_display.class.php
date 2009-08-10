<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
 require_once dirname(__FILE__).'/../../../laika_manager.class.php';
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class LaikaWizardDisplay extends HTML_QuickForm_Action_Display
{
	/**
	 * The repository tool in which the wizard runs
	 */
	private $parent;
	/**
	 * Constructor
	 * @param Tool $parent The repository tool in which the wizard
	 * runs
	 */
	public function LaikaWizardDisplay($parent)
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
		$trail->add(new Breadcrumb($this->parent->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
		$trail->add(new Breadcrumb($this->parent->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_TAKE_TEST)), Translation :: get('TakeLaika')));
		
		$this->parent->display_header($trail);
		if(isset($_SESSION['build_message']))
		{
			Display :: normal_message($_SESSION['build_message']);
			unset($_SESSION['build_message']);
		}
		if(isset($_SESSION['build_error_message']))
		{
			Display :: error_message($_SESSION['build_error_message']);
			unset($_SESSION['build_error_message']);
		}
		parent::_renderForm($current_page);
		$this->parent->display_footer();
	}
}
?>