<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/subscription_overview_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/subscription_table/default_subscription_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../subscription.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscriptionOverviewBrowserTableCellRenderer extends DefaultSubscriptionTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function SubscriptionOverviewBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $subscription)
	{
		if(!$this->reservation || $this->reservation->get_id() != $subscription->get_reservation_id())
		{
			$this->reservation = $this->browser->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $subscription->get_reservation_id()))->next_result();
		}
		
		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case Translation :: get(DokeosUtilities :: underscores_to_camelcase(Subscription :: PROPERTY_USER_ID)) :
					$user = UserDataManager :: get_instance()->retrieve_user($subscription->get_user_id());
					return $user->get_fullname();
				case Translation :: get(DokeosUtilities :: underscores_to_camelcase(Subscription :: PROPERTY_RESERVATION_ID)) :
				{
					$item = $this->browser->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $this->reservation->get_item()))->next_result();
					return $item->get_name();
				}
			}
		}
		
		return parent :: render_cell($column, $subscription);
	}
}
?>