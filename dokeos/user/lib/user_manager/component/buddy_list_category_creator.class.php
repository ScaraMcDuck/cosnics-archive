<?php
/**
 * @package reservations.lib.categorymanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/buddy_list_category_form.class.php';

class UserManagerBuddyListCategoryCreatorComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();
		$category = new BuddyListCategory();
		
		$form = new BuddyListCategoryForm(BuddyListCategoryForm :: TYPE_CREATE, $this->get_url(), $category, $user, $this);

		if($form->validate())
		{
			$success = $form->create_category();
			$this->redirect(Translation :: get($success ? 'BuddyListCategoriesCreated' : 'BuddyListCategoriesNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
		}
		else
		{
			$trail = new BreadcrumbTrail();
			$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST)), Translation :: get('BuddyList')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddBuddyListCategories')));
		
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>