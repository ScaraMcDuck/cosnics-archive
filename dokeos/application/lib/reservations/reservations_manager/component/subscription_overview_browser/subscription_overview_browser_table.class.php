<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/subscription_overview_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/subscription_overview_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/subscription_overview_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class SubscriptionOverviewBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'reservations_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function SubscriptionOverviewBrowserTable($browser, $parameters, $condition)
	{
		$model = new SubscriptionOverviewBrowserTableColumnModel($browser);
		$renderer = new SubscriptionOverviewBrowserTableCellRenderer($browser);
		$data_provider = new SubscriptionOverviewBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, SubscriptionOverviewBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}
}
?>