<?php
/**
 * @package application.lib.profiler.profiler_manager.component.profilepublicationbrowser
 */
require_once dirname(__FILE__).'/../../../profile_publication_table/profile_publication_table.class.php';
require_once dirname(__FILE__).'/profile_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/profile_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/profile_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../profiler.class.php';
/**
 * Table to display a set of learning objects.
 */
class ProfilePublicationBrowserTable extends ProfilePublicationTable
{
	/**
	 * Constructor
	 */
	function ProfilePublicationBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new ProfilePublicationBrowserTableColumnModel();
		$renderer = new ProfilePublicationBrowserTableCellRenderer($browser);
		$data_provider = new ProfilePublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$actions = array();
		$actions[Profiler :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
		if ($browser->get_user()->is_platform_admin())
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>