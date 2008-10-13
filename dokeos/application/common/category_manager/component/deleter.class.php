<?php
/**
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';

/**
 * Component to delete a category
 */
class CategoryManagerCategoryDeleterComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		$ids = $_GET[CategoryManager :: PARAM_CATEGORY_ID];
		
		if (!$this->get_user())
		{
			$this->display_header(null);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if($ids)
		{ 
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			$bool = true;
			$parent = -1;
			
			foreach($ids as $id)
			{
    			$categories = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_ID, $id));
    			$category = $categories->next_result();
    			
    			if($parent == -1) $parent = $category->get_parent();
    			if(!$category->delete()) $bool = false;
			}
			
			if(count($ids) == 1)
				$message = $bool ? 'CategoryDeleted' : 'CategoryNotDeleted';
			else
				$message = $bool ? 'CategoriesDeleted' : 'CategoriesNotDeleted';
			
			
			$this->redirect('url', Translation :: get($message), ($bool ? false : true), 
				array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES,
					  CategoryManager :: PARAM_CATEGORY_ID => $parent));
		}
		else
		{
			$this->display_header();
			$this->display_error_message(Translation :: get("NoObjectSelected"));
			$this->display_footer();
		}
	}

}
?>