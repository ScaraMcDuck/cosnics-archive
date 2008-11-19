<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';
require_once dirname(__FILE__).'/../platform_category.class.php';
require_once dirname(__FILE__).'/../category_form.class.php';

class CategoryManagerMoverComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[CategoryManager :: PARAM_CATEGORY_ID];
		$direction = $_GET[CategoryManager :: PARAM_DIRECTION];
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Browse Categories')));
		$trail->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Create category')));

		$user = $this->get_user();

		if (!isset($user) || !isset($category_id)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$categories = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $category_id));
		$category = $categories->next_result();
		$parent = $category->get_parent();
		
		$display_order = $category->get_display_order();
		$new_place = $display_order + $direction;
		$category->set_display_order($new_place);
		
		$conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_DISPLAY_ORDER, $new_place);
		$conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent);
		$condition = new AndCondition($conditions);
		$categories = $this->retrieve_categories($condition);
		$newcategory = $categories->next_result();
		
		$newcategory->set_display_order($display_order);
		
		$sucess = true;
		
		if(!$category->update() || !$newcategory->update())
		{
			$sucess = false;
		}
		if(get_class($this->get_parent()) == 'RepositoryCategoryManager')
			$this->repository_redirect(RepositoryManager :: ACTION_MANAGE_CATEGORIES, Translation :: get($sucess ? 'CategoryMoved' : 'CategoryNotMoved'), 0, ($sucess ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_parent()));
		else
			$this->redirect('url', Translation :: get($sucess ? 'CategoryMoved' : 'CategoryNotMoved'), ($sucess ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_parent()));
	}
}
?>