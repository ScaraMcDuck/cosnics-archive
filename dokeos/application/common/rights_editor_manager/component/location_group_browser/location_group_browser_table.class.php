<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/location_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/location_group_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/location_group_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class LocationGroupBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'location_group_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function LocationGroupBrowserTable($browser, $parameters, $condition)
	{
		$model = new LocationGroupBrowserTableColumnModel($browser);
		$renderer = new LocationGroupBrowserTableCellRenderer($browser);
		$data_provider = new LocationGroupBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, LocationGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}
}
?>