<?php
/**
 * @package application.lib.profiler.profiler_manager.component.profilepublicationbrowser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/system_announcement_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/system_announcement_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/system_announcement_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../admin_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class SystemAnnouncementPublicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'system_announcement_publication_browser_table';
	
	/**
	 * Constructor
	 */
	function SystemAnnouncementPublicationBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new SystemAnnouncementPublicationBrowserTableColumnModel();
		$renderer = new SystemAnnouncementPublicationBrowserTableCellRenderer($browser);
		$data_provider = new SystemAnnouncementPublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, SystemAnnouncementPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		$actions[Admin :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
		if ($browser->get_user()->is_platform_admin())
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>