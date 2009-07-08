<?php
/**
 * @package portfolio.lib.portfoliomanager.component.user_browser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/user_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../portfolio_manager.class.php';

/**
 * Table to display a set of users.
 */
class UserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'user_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function UserBrowserTable($browser, $parameters, $condition)
	{
		$model = new UserBrowserTableColumnModel();
		$renderer = new UserBrowserTableCellRenderer($browser);
		$data_provider = new UserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, UserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>