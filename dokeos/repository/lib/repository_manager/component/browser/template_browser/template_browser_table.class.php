<?php
/**
 * $Id: repository_browser_table.class.php 22935 2009-08-25 12:16:30Z vanpouckesven $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/template_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/template_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../repository_browser_table.class.php';
/**
 * Table to display a set of learning objects.
 */
class TemplateBrowserTable extends RepositoryBrowserTable
{
	const DEFAULT_NAME = 'template_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function TemplateBrowserTable($browser, $parameters, $condition)
	{
		$model = new TemplateBrowserTableColumnModel();
		$renderer = new TemplateBrowserTableCellRenderer($browser);
		$data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
		parent :: ObjectTable($data_provider, TemplateBrowserTable :: DEFAULT_NAME, $model, $renderer);
		
		$actions = array();
		$actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_DELETE_TEMPLATES, Translation :: get('RemoveSelected'));
		$actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_COPY_FROM_TEMPLATES, Translation :: get('CopySelectedToRepository'), false);
		$this->set_form_actions($actions);
		
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}
}
?>