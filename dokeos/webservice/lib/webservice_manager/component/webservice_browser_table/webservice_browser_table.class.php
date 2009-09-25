<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/webservice_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/webservice_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/webservice_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../webservice_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class WebserviceBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'webservice_browser_table';
	
	/**
	 * Constructor
	 * @see ContentObjectTable::ContentObjectTable()
	 */
	function WebserviceBrowserTable($browser, $parameters, $condition)
	{
		$model = new WebserviceBrowserTableColumnModel();
		$renderer = new WebserviceBrowserTableCellRenderer($browser);
		$data_provider = new WebserviceBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, WebserviceBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}
	
}
?>