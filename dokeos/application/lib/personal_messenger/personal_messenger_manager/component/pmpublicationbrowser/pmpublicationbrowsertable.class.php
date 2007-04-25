<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../pm_publication_table/pmpublicationtable.class.php';
require_once dirname(__FILE__).'/pmpublicationbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/pmpublicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/pmpublicationbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../personal_messenger.class.php';
/**
 * Table to display a set of learning objects.
 */
class PmPublicationBrowserTable extends PmPublicationTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function PmPublicationBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new PmPublicationBrowserTableColumnModel($browser->get_folder());
		$renderer = new PmPublicationBrowserTableCellRenderer($browser);
		$data_provider = new PmPublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[PersonalMessenger :: PARAM_DELETE_SELECTED] = get_lang('RemoveSelected');
		$actions[PersonalMessenger :: PARAM_MARK_SELECTED_READ] = get_lang('MarkSelectedRead');
		$actions[PersonalMessenger :: PARAM_MARK_SELECTED_UNREAD] = get_lang('MarkSelectedUnread');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>