<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../tables/subscription_table/default_subscription_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../subscription.class.php';
/**
 * Table column model for the user browser table
 */
class SubscriptionBrowserTableColumnModel extends DefaultSubscriptionTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function SubscriptionBrowserTableColumnModel($browser)
	{
		parent :: __construct($browser);
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
