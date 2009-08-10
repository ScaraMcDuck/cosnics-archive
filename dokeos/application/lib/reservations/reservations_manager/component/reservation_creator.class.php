<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../reservation.class.php';
require_once dirname(__FILE__).'/../../forms/reservation_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';

class ReservationsManagerReservationCreatorComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$item_id = $_GET[ReservationsManager :: PARAM_ITEM_ID];
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('View items')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $item_id)), Translation :: get('View reservations')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $item_id)), Translation :: get('Create reservation')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$reservation = new Reservation();
		$reservation->set_item(isset($item_id)?$item_id:0);
		
		$form = new ReservationForm(ReservationForm :: TYPE_CREATE, $this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $item_id)), $reservation, $user);
		$status = $form->allow_create_reservation();
		
		if($status == 1)
		{
			$success = $form->create_reservation();
			$this->redirect(Translation :: get($success ? 'ReservationCreated' : 'ReservationNotCreated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $item_id));
		}
		else
		{
			$_GET['message'] = $this->parse_status($status);
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
	
	function parse_status($status)
	{
		switch($status)
		{
			case 2: return Translation :: get('ReservationDateNotFree');
			case 3: return Translation :: get('SubscriptionEndAfterStart');
			case 4: return Translation :: get('StartDateInPast');
			case 5: return Translation :: get('NoEqualDatesWithTimepicker');
			case 6: return Translation :: get('MinLargerMax');
			case 7: return Translation :: get('BlockToLarge');
			case 8: return Translation :: get('CantRepeat');
		}
	}
}
?>