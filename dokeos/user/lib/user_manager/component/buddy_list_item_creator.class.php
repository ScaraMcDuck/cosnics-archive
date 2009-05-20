<?php
/**
 * @package reservations.lib.categorymanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/buddy_list_item_form.class.php';

class UserManagerBuddyListItemCreatorComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();
		$item = new BuddyListItem();
		$form = new BuddyListItemForm($user, $this->get_url());

		if($form->validate())
		{
			$success = $form->create_items();
			$this->redirect(Translation :: get($success ? 'BuddyListItemsCreated' : 'BuddyListItemsNotCreated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
		}
		else
		{
			$trail = new BreadcrumbTrail();
			$trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST)), Translation :: get('BuddyList')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddBuddies')));
		
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>