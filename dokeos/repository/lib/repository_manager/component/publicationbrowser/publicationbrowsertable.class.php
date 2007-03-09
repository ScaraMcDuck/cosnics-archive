<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../publication_table/publicationtable.class.php';
require_once dirname(__FILE__).'/publicationbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/publicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/publicationbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../repositorymanager.class.php';
/**
 * Table to display a set of learning objects.
 */
class PublicationBrowserTable extends PublicationTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function PublicationBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new PublicationBrowserTableColumnModel();
		$renderer = new PublicationBrowserTableCellRenderer($browser);
		$data_provider = new PublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[RepositoryManager :: PARAM_RECYCLE_SELECTED] = get_lang('RemoveSelected');
		//$actions[RepositoryManager :: PARAM_MOVE_SELECTED] = get_lang('MoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>