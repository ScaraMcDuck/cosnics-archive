<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/location_rights_template_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/location_rights_template_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/location_rights_template_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../rights_template_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class LocationRightsTemplateBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'rights_template_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function LocationRightsTemplateBrowserTable($browser, $parameters, $condition)
	{
		$model = new LocationRightsTemplateBrowserTableColumnModel($browser);
		$renderer = new LocationRightsTemplateBrowserTableCellRenderer($browser);
		$data_provider = new LocationRightsTemplateBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, LocationRightsTemplateBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}
	
	function get_objects($offset, $count, $order_column, $order_direction)
	{
		$rights_templates = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)), $order_direction);
		$table_data = array ();
		$column_count = $this->get_column_model()->get_column_count();
		while ($rights_template = $rights_templates->next_result())
		{
			$row = array ();
			if ($this->has_form_actions())
			{
				$row[] = $rights_template->get_name();
			}
			for ($i = 0; $i < $column_count; $i ++)
			{
				$row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $rights_template);
			}
			$table_data[] = $row;
		}
		return $table_data;
	}
}
?>