<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learning_object_table.class.php';
require_once dirname(__FILE__).'/repository_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/repository_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/repository_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../repository_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class RepositoryBrowserTable extends LearningObjectTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function RepositoryBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new RepositoryBrowserTableColumnModel();
		$renderer = new RepositoryBrowserTableCellRenderer($browser);
		$data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[RepositoryManager :: PARAM_RECYCLE_SELECTED] = Translation :: get('RemoveSelected');
		$actions[RepositoryManager :: PARAM_MOVE_SELECTED] = Translation :: get('MoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>