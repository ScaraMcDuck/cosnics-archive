<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../tables/subscription_user_table/default_subscription_user_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../subscription.class.php';
/**
 * Table column model for the user browser table
 */
class SubscriptionUserBrowserTableColumnModel extends DefaultSubscriptionUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function SubscriptionUserBrowserTableColumnModel($browser)
	{
		parent :: __construct($browser);
		$this->set_default_order_column(1);
	}

}
?>
