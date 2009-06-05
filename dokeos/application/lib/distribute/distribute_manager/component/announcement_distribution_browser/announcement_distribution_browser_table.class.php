<?php
/**
 * @package application.distribute
 * @author Hans De Bisschop
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once dirname(__FILE__).'/announcement_distribution_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/announcement_distribution_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/announcement_distribution_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../distribute_manager.class.php';
/**
 * Table to display a set of announcement distributions.
 */
class AnnouncementDistributionBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'announcement_distribution_browser_table';

	/**
	 * Constructor
	 */
	function AnnouncementDistributionBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new AnnouncementDistributionBrowserTableColumnModel();
		$renderer = new AnnouncementDistributionBrowserTableCellRenderer($browser);
		$data_provider = new AnnouncementDistributionBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, AnnouncementDistributionBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		//$actions[DistributeManager :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>