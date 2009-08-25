<?php
/**
 * $Id:$
 * @package application.weblcms.weblcms_manager.component
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/subscribed_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/subscribed_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/subscribed_user_browser_table_cell_renderer.class.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';
/**
 * Table to display a list of users not subscribed to a course.
 */
class SubscribedUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'subscribed_user_browser_table';
	
	/**
	 * Constructor
	 */
	function SubscribedUserBrowserTable($browser, $parameters, $condition)
	{
		$model = new SubscribedUserBrowserTableColumnModel();
		$renderer = new SubscribedUserBrowserTableCellRenderer($browser);
		$data_provider = new SubscribedUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, SubscribedUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		if (Request :: get(WeblcmsManager :: PARAM_TOOL_ACTION) != WeblcmsManager :: ACTION_SUBSCRIBE)
		{
			$actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'), false);
		}
		else
		{
			$actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT, Translation :: get('SubscribeSelectedAsStudent'), false);
			$actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN, Translation :: get('SubscribeSelectedAsAdmin'), false);
		}
		
		//$actions[] = new ObjectTableFormAction(UserTool :: ACTION_USER_DETAILS, Translation :: get('Details'), false);
		
		if ($browser->get_course()->is_course_admin($browser->get_user()))
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>