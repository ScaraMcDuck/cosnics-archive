<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../item.class.php';
require_once dirname(__FILE__).'/../../forms/item_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ReservationsManagerItemUpdaterComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
		$item_id = $_GET[ReservationsManager :: PARAM_ITEM_ID];
		
		$trail = new BreadcrumbTrail();
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('View items')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $item_id, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Update item')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$items = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $item_id));
		$item = $items->next_result();
		
		$form = new ItemForm(ItemForm :: TYPE_EDIT, $this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $item->get_id(),ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), $item, $user);

		if($form->validate())
		{
			$success = $form->update_item();
			$this->redirect('url', Translation :: get($success ? 'ItemUpdated' : 'ItemNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
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