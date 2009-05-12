<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component.pmpublicationbrowser
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once dirname(__FILE__).'/pm_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/pm_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/pm_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../personal_messenger.class.php';
/**
 * Table to display a set of pm publications.
 */
class PmPublicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'pm_publication_browser_table';
	
	/**
	 * Constructor
	 */
	function PmPublicationBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new PmPublicationBrowserTableColumnModel($browser->get_folder());
		$renderer = new PmPublicationBrowserTableCellRenderer($browser);
		$data_provider = new PmPublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, PmPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		$actions[PersonalMessenger :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
		if(Request :: get_folder('folder') == 'inbox')
		{
			$actions[PersonalMessenger :: PARAM_MARK_SELECTED_READ] = Translation :: get('MarkSelectedRead');
			$actions[PersonalMessenger :: PARAM_MARK_SELECTED_UNREAD] = Translation :: get('MarkSelectedUnread');
		}
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>