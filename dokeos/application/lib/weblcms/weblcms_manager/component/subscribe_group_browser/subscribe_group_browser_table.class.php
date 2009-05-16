<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/subscribe_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/subscribe_group_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/subscribe_group_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class SubscribeGroupBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'subscribe_group_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function SubscribeGroupBrowserTable($browser, $parameters, $condition)
	{
		$model = new SubscribeGroupBrowserTableColumnModel();
		$renderer = new SubscribeGroupBrowserTableCellRenderer($browser);
		$data_provider = new SubscribeGroupBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, SubscribeGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[WeblcmsManager :: PARAM_SUBSCRIBE_SELECTED_GROUP] = Translation :: get('SubscribeSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>