<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/repository_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/repository_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/repository_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../repository_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class RepositoryBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'repository_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function RepositoryBrowserTable($browser, $parameters, $condition)
	{
		$model = new RepositoryBrowserTableColumnModel();
		$renderer = new RepositoryBrowserTableCellRenderer($browser);
		$data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, RepositoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
		if(get_class($browser) == 'RepositoryManagerBrowserComponent')
		{
			$actions = array();
			$actions[RepositoryManager :: PARAM_RECYCLE_SELECTED] = Translation :: get('RemoveSelected');
			$actions[RepositoryManager :: PARAM_MOVE_SELECTED] = Translation :: get('MoveSelected');
		}
		if(get_class($browser) == 'RepositoryManagerLearningObjectSelectorComponent')
		{
			$actions = array();
			$actions[RepositoryManager :: PARAM_ADD_OBJECTS] = Translation :: get('AddObjects');
		}
		$this->set_additional_parameters($parameters);
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>