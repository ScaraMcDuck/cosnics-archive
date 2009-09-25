<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/subscription_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/subscription_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/subscription_user_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class SubscriptionUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'reservations_table';
	
	/**
	 * Constructor
	 * @see ContentObjectTable::ContentObjectTable()
	 */
	function SubscriptionUserBrowserTable($browser, $parameters, $condition)
	{
		$model = new SubscriptionUserBrowserTableColumnModel($browser);
		$renderer = new SubscriptionUserBrowserTableCellRenderer($browser);
		$data_provider = new SubscriptionUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, SubscriptionUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		
		$this->set_default_row_count(20);
	}
	
	function get_objects($offset, $count, $order_column, $order_direction)
	{
		$subscription_users = $this->get_data_provider()->get_objects();
		$table_data = array ();
		$column_count = $this->get_column_model()->get_column_count();
		while ($su = $subscription_users->next_result())
		{
			$row = array ();
			if ($this->has_form_actions())
			{
				$row[] = $su->get_user_id();
			}
			for ($i = 0; $i < $column_count; $i ++)
			{
				$row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $su);
			}
			$table_data[] = $row;
		}
		return $table_data;
	}
}
?>