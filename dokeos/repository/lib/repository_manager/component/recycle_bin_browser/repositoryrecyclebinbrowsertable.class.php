<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttable.class.php';
require_once dirname(__FILE__).'/repositoryrecyclebinbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/repositoryrecyclebinbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/repositoryrecyclebinbrowsertablecellrenderer.class.php';

class RepositoryRecycleBinBrowserTable extends LearningObjectTable
{
	function RepositoryRecycleBinBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new RepositoryRecycleBinBrowserTableColumnModel();
		$renderer = new RepositoryRecycleBinBrowserTableCellRenderer($browser);
		$data_provider = new RepositoryRecycleBinBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[RepositoryManager :: PARAM_RESTORE_SELECTED] = get_lang('RestoreSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>