<?php
/**
 * $Id:$
 * @package application.weblcms.weblcms_manager.component
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/subscribe_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/subscribe_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/subscribe_user_browser_table_cell_renderer.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/user_manager.class.php';
/**
 * Table to display a list of users not subscribed to a course.
 */
class SubscribeUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'subscribe_user_browser_table';
	
	/**
	 * Constructor
	 */
	function SubscribeUserBrowserTable($browser, $parameters, $condition)
	{
		$model = new SubscribeUserBrowserTableColumnModel();
		$renderer = new SubscribeUserBrowserTableCellRenderer($browser);
		$data_provider = new SubscribeUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, SubscribeUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[ClassGroupManager :: PARAM_SUBSCRIBE_SELECTED] = Translation :: get('Subscribe');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>