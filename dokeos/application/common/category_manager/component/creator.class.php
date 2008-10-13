<?php
/**
 * @package reservations.lib.categorymanager.component
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';
require_once dirname(__FILE__).'/../category.class.php';
require_once dirname(__FILE__).'/../category_form.class.php';

class CategoryManagerCreatorComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[CategoryManager :: PARAM_CATEGORY_ID];
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Browse Categories')));
		$trail->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Create category')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$category = new Category();
		$category->set_parent(isset($category_id)?$category_id:0);
		
		$form = new CategoryForm(CategoryForm :: TYPE_CREATE, $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category_id)), $category, $user);

		if($form->validate())
		{
			$success = $form->create_category();
			$this->redirect('url', Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>