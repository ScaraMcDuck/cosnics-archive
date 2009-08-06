<?php
/**
 * $Id: repository_browser_table.class.php 17558 2009-01-07 11:37:10Z vanpouckesven $
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/repository_shared_learning_objects_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/repository_shared_learning_objects_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/repository_shared_learning_objects_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../repository_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class RepositorySharedLearningObjectsBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'repository_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function RepositorySharedLearningObjectsBrowserTable($browser, $parameters, $condition)
	{
		$model = new RepositorySharedLearningObjectsBrowserTableColumnModel();
		$renderer = new RepositorySharedLearningObjectsBrowserTableCellRenderer($browser);
		$data_provider = new RepositorySharedLearningObjectsBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, RepositorySharedLearningObjectsBrowserTable :: DEFAULT_NAME, $model, $renderer);
		if(get_class($browser) == 'RepositoryManagerBrowserComponent')
		{
			$actions = array();
			$actions[RepositoryManager :: PARAM_RECYCLE_SELECTED] = Translation :: get('RemoveSelected');
			$actions[RepositoryManager :: PARAM_MOVE_SELECTED] = Translation :: get('MoveSelected');
			$actions[RepositoryManager :: PARAM_PUBLISH_SELECTED] = Translation :: get('PublishSelected');
		}
		if(get_class($browser) == 'RepositoryManagerComplexBrowserComponent')
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