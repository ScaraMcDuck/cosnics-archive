<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';
require_once dirname(__FILE__).'/../platform_category.class.php';
require_once dirname(__FILE__).'/../category_form.class.php';

class CategoryManagerUpdaterComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[CategoryManager :: PARAM_CATEGORY_ID];
		$user = $this->get_user();

		$categories = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $category_id));
		$category = $categories->next_result();
		
		$form = new CategoryForm(CategoryForm :: TYPE_EDIT, $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category->get_id())), $category, $user);

		if($form->validate())
		{
			$success = $form->update_category();
			if(get_class($this->get_parent()) == 'RepositoryCategoryManager')
				$this->repository_redirect(RepositoryManager :: ACTION_MANAGE_CATEGORIES, Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), 0, ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_parent()));
			else
				$this->redirect('url', Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_parent()));
		}
		else
		{
			$form->display();
		}
	}
}
?>