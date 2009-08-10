<?php
/**
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';

/**
 * Component to delete a category
 */
class ReservationsManagerCategoryDeleterComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		$ids = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
		
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
			$db = ReservationsDataManager :: get_instance();
			
			foreach($ids as $id)
			{
    			$categories = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_ID, $id));
    			$category = $categories->next_result();
    			
    			if($parent == -1) $parent = $category->get_parent();
    			
    			$category->set_status(Category :: STATUS_DELETED);
    			
    			$db->clean_display_order($category);
    			
    			$category->set_display_order(0);
    			if(!$category->update()) $bool = false;
			}
			
			if(count($ids) == 1)
				$message = $bool ? 'CategoryDeleted' : 'CategoryNotDeleted';
			else
				$message = $bool ? 'CategoriesDeleted' : 'CategoriesNotDeleted';
			
			
			$this->redirect('url', Translation :: get($message), ($bool ? false : true), 
				array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES,
					  ReservationsManager :: PARAM_CATEGORY_ID => $parent));
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