<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/quota_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/quota_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/quota_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class QuotaBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'reservations_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function QuotaBrowserTable($browser, $parameters, $condition)
	{
		$model = new QuotaBrowserTableColumnModel();
		$renderer = new QuotaBrowserTableCellRenderer($browser);
		$data_provider = new QuotaBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, QuotaBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		
		if($browser->get_user() && $browser->get_user()->is_platform_admin())
		{
			$actions = array();
			$actions[ReservationsManager :: PARAM_REMOVE_SELECTED_QUOTAS] = Translation :: get('RemoveSelected');
			$this->set_form_actions($actions);
		}
		
		$this->set_default_row_count(20);
	}
}
?>