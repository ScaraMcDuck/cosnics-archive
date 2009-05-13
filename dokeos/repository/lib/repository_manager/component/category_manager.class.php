<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../category_manager/repository_category_manager.class.php';

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerCategoryManagerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
        $trail = new BreadCrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));

        $category_manager = new RepositoryCategoryManager($this,$trail);
		$category_manager->run();
	}
}
?>