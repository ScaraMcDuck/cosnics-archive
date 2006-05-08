<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttable.class.php';
require_once dirname(__FILE__).'/repositorybrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/repositorybrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/repositorybrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../repositorymanager.class.php';

class RepositoryBrowserTable extends LearningObjectTable
{
	function RepositoryBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new RepositoryBrowserTableColumnModel();
		$renderer = new RepositoryBrowserTableCellRenderer($browser);
		$data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[RepositoryManager :: PARAM_RECYCLE_SELECTED] = get_lang('RemoveSelected');
		$actions[RepositoryManager :: PARAM_MOVE_SELECTED] = get_lang('MoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>