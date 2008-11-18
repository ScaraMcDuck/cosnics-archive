<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../../category_manager/admin_category_manager.class.php';

/**
 * Weblcms component allows the user to manage course categories
 */
class AdminCategoryManagerComponent extends AdminManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_manager = new AdminCategoryManager($this);
		$category_manager->run();
	}
}
?>