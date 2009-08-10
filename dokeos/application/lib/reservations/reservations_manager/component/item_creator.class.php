<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../item.class.php';
require_once dirname(__FILE__).'/../../forms/item_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';

class ReservationsManagerItemCreatorComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('View items')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Create item')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$item = new Item();
		$item->set_creator($this->get_user_id());
		$item->set_category(isset($category_id)?$category_id:0);
		
		$form = new ItemForm(ItemForm :: TYPE_CREATE, $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), $item, $user);

		if($form->validate())
		{
			$success = $form->create_item();
			$this->redirect(Translation :: get($success ? 'ItemCreated' : 'ItemNotCreated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
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