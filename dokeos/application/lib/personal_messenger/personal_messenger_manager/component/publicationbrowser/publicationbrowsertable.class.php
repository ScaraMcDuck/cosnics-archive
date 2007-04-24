<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../publication_table/publicationtable.class.php';
require_once dirname(__FILE__).'/publicationbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/publicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/publicationbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../personal_messenger.class.php';
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
		$model = new PublicationBrowserTableColumnModel($browser->get_folder());
		$renderer = new PublicationBrowserTableCellRenderer($browser);
		$data_provider = new PublicationBrowserTableDataProvider($browser, $condition);
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