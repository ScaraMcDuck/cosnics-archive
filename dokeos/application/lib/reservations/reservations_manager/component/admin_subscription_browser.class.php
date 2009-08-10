<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/subscription_browser/subscription_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../reservations_menu.class.php';

class ReservationsManagerAdminSubscriptionBrowserComponent extends ReservationsManagerComponent
{
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadCrumbTrail();
		
		$reservation = $this->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $this->get_reservation_id()))->next_result();
		$item = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $reservation->get_item()))->next_result();
		
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('View items')));
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $reservation->get_item())), Translation :: get('View reservations')));
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_RESERVATION_ID => $this->get_reservation_id())), Translation :: get('View subscriptions')));
		
		$this->display_header($trail);
		
		$this->display_reservation_information($item, $reservation);
		
		echo $this->get_user_html();
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new SubscriptionBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS), $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$reservation_id = $this->get_reservation_id();
		
		$conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation_id);
		$conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
		$condition = new AndCondition($conditions);
		
		return $condition;
	}
	
	function get_reservation_id()
	{
		$reservation_id = $_GET[ReservationsManager :: PARAM_RESERVATION_ID];
		return $reservation_id?$reservation_id:0;
	}
	
	function display_reservation_information($item, $reservation)
	{
		$responsible = UserDataManager :: get_instance()->retrieve_user($item->get_responsible())->get_fullname();
		
		$html[] = '<div class="learning_object" style="background-image: url(' . Theme :: get_theme_path().'treemenu_types/calendar_event.png);">';
		$html[] = '<div class="title">';
		$html[] = $item->get_name();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $item->get_description();
		$html[] = '<b>' . Translation :: get('Responsible') . '</b>: ' . $responsible;
		$html[] = '<br /><b>' . Translation :: get('Type') . '</b>: ' . $this->get_type($reservation);
		$html[] = '<br /><b>' . Translation :: get('Start') . '</b>: ' . $reservation->get_start_date();
		$html[] = '<br /><b>' . Translation :: get('End') . '</b>: ' . $reservation->get_stop_date();
		$html[] = '</div>';
		$html[] = '</div>';
		echo implode("\n", $html);
	}
	
	function get_type($reservation)
	{
		switch($reservation->get_type())
		{
			case Reservation :: TYPE_TIMEPICKER: return Translation :: get('Timepicker');
			case Reservation :: TYPE_BLOCK: return Translation :: get('Block');
		}
	}
}