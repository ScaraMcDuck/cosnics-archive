<?php
/**
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';
require_once Path :: get_admin_path() .'lib/admin_data_manager.class.php';

/**
 * Component to delete a category
 */
class CategoryManagerGeneralCategoriesCopierComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		
		$adm = AdminDataManager :: get_instance();
		$categories = $adm->retrieve_categories();
		
		$bool = true;
		$ids = array(0 => 0);
		
		while($category = $categories->next_result())
		{
			$newcat = $this->get_category();
			$newcat->set_name($category->get_name());
			$newcat->set_parent($ids[$category->get_parent()]);
			if(!$newcat->create())
			{
				$bool = false;
			}
			$ids[$category->get_id()] = $newcat->get_id();
		}
		
		if($bool)
			$message = 'GeneralCategoriesCopied';
		else
			$message = 'GeneralCategoriesNotCopied';
		
		$this->redirect('url', Translation :: get($message), ($bool ? false : true), 
			array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES));

	}

}
?>