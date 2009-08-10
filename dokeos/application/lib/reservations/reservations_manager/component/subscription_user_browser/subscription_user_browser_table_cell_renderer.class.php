<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/subscription_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/subscription_user_table/default_subscription_user_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../subscription_user.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscriptionUserBrowserTableCellRenderer extends DefaultSubscriptionUserTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function SubscriptionUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $subscription_user)
	{
		return parent :: render_cell($column, $subscription_user);
	}
}
?>