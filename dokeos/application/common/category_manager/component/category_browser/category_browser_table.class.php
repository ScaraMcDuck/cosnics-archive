<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/category_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/category_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/category_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../category_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class CategoryBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'category_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function CategoryBrowserTable($browser, $parameters, $condition)
	{
		$model = new CategoryBrowserTableColumnModel();
		$renderer = new CategoryBrowserTableCellRenderer($browser);
		$data_provider = new CategoryBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, CategoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		
		if($browser->get_user() && $browser->get_user()->is_platform_admin())
		{
			$actions = array();
			$actions[CategoryManager :: PARAM_REMOVE_SELECTED_CATEGORIES] = Translation :: get('RemoveSelected');
			$actions[CategoryManager :: PARAM_MOVE_SELECTED_CATEGORIES] = Translation :: get('MoveSelected');
			$this->set_form_actions($actions);
		}
		
		$this->set_default_row_count(20);
	}
}
?>