<?php
/**
 * $Id: repository_browser_table.class.php 15472 2008-05-27 18:47:47Z Scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/complex_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/complex_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/complex_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../repository_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class ComplexBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'repository_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function ComplexBrowserTable($browser, $parameters, $condition, $show_subitems_column = true)
	{
		$model = new ComplexBrowserTableColumnModel($show_subitems_column);
		$renderer = new ComplexBrowserTableCellRenderer($browser, $condition);
		$data_provider = new ComplexBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, RepositoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters); 
		$actions = array();
		
		if(get_parent_class($browser) == 'ComplexBuilder')
		{
			$actions[ComplexBuilder :: PARAM_DELETE_SELECTED_CLOI] = Translation :: get('RemoveSelected');
		}
		else
		{
			$actions[RepositoryManager :: PARAM_REMOVE_SELECTED_CLOI] = Translation :: get('RemoveSelected');
		}
		
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>