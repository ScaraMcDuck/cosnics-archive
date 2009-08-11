<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../item.class.php';
require_once dirname(__FILE__).'/../../subscription.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * TODO: Add comment
 */
class DefaultSubscriptionTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultSubscriptionTableCellRenderer($browser)
	{
		
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $subscription)
	{
		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case Subscription :: PROPERTY_ID :
					return $subscription->get_id();
				case Subscription :: PROPERTY_USER_ID :
					$user = UserDataManager :: get_instance()->retrieve_user($subscription->get_user_id());
					return $user->get_fullname();
				case Subscription :: PROPERTY_RESERVATION_ID :
				{
					$reservation = $this->browser->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $subscription->get_reservation_id()))->next_result();
					$item = $this->browser->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $reservation->get_item()))->next_result();
					return $item->get_name();
				}
				case Subscription :: PROPERTY_START_TIME :
				{
					$t = $subscription->get_start_time();
					if(!$t)
					{
						$reservation = $this->browser->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $subscription->get_reservation_id()))->next_result();
						$t = $reservation->get_start_date();
					}
					return $t;
				}
				case Subscription :: PROPERTY_STOP_TIME :
				{
					$t = $subscription->get_stop_time();
					if(!$t)
					{
						$reservation = $this->browser->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $subscription->get_reservation_id()))->next_result();
						$t = $reservation->get_stop_date();
					}
					return $t;
				}
				case Subscription :: PROPERTY_ACCEPTED :
					if($subscription->get_accepted())
						return Translation :: get('Yes');
					
					return Translation :: get('No');
			}

		}
			
		return '&nbsp;';
	}
	
	function render_id_cell($subscription)
	{
		return $subscription->get_id();
	}
}
?>