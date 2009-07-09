<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/help_item_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/help_item_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/help_item_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../help_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class HelpItemBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'help_item_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function HelpItemBrowserTable($browser, $parameters, $condition)
	{
		$model = new HelpItemBrowserTableColumnModel();
		$renderer = new HelpItemBrowserTableCellRenderer($browser);
		$data_provider = new HelpItemBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, HelpItemBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}
	
	function get_objects($offset, $count, $order_column, $order_direction)
	{
		$help_items = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)), $order_direction);
		$table_data = array ();
		$column_count = $this->get_column_model()->get_column_count();
		while ($help_item = $help_items->next_result())
		{
			$row = array ();
			if ($this->has_form_actions())
			{
				$row[] = $help_item->get_name();
			}
			for ($i = 0; $i < $column_count; $i ++)
			{
				$row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $help_item);
			}
			$table_data[] = $row;
		}
		return $table_data;
	}
}
?>