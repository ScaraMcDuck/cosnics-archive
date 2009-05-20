<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';

class UserManagerBuddyListCategoryDeleterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(UserManager :: PARAM_BUDDYLIST_CATEGORY);
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$cat = new BuddyListCategory();
				$cat->set_id($id);
				
				if(!$cat->delete())
				{
					$failures++;
				}
			}
			
			if (!$failures)
			{
				if (count($ids) == 1)
				{
					$message = 'BuddyListCategoriesDeleted';
				}
				else
				{
					$message = 'BuddyListCategoryDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'BuddyListCategoriesNotDeleted';
				}
				else
				{
					$message = 'BuddyListCategoryNotDeleted';
				}
				
				echo $message;
			}
		
			$ajax = Request :: get('ajax');
			if(!$ajax)
				$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoBuddyListCategoriesSelected')));
		}
	}
}
?>