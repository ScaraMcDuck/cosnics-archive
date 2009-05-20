<?php

require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/buddy_list_category_form.class.php';

class UserManagerBuddyListCategoryEditorComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$id = Request :: get(UserManager :: PARAM_BUDDYLIST_CATEGORY);
		if ($id)
		{
			$category = UserDataManager :: get_instance()->retrieve_buddy_list_categories(new EqualityCondition('id', $id))->next_result();
			$form = new BuddyListCategoryForm(BuddyListCategoryForm :: TYPE_EDIT, $this->get_url(array(UserManager :: PARAM_BUDDYLIST_CATEGORY => $id)), $category, $this->get_user(), $this);

			if($form->validate())
			{
				$success = $form->update_category();
				$this->redirect(Translation :: get($success ? 'CategoryUpdated' : 'CategoryNotUpdated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
			}
			else
			{
				$trail = new BreadcrumbTrail();
				$trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST)), Translation :: get('BuddyList')));
				$trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_BUDDYLIST_CATEGORY => $id)), Translation :: get('UpdateBuddyListCategory')));
			
				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCategorySelected')));
		}
	}
}
?>