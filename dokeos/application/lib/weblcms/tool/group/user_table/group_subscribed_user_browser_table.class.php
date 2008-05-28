<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/group_subscribed_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/group_subscribed_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/group_subscribed_user_browser_table_cell_renderer.class.php';

class GroupSubscribedUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'group_subscribed_user_browser_table';
	
	/**
	 * Constructor
	 */
	function GroupSubscribedUserBrowserTable($browser, $parameters, $condition)
	{
		$model = new GroupSubscribedUserBrowserTableColumnModel();
		$renderer = new GroupSubscribedUserBrowserTableCellRenderer($browser);
		$data_provider = new GroupSubscribedUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, GroupSubscribedUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		if ($_GET[Weblcms :: PARAM_USER_ACTION] != Weblcms :: ACTION_SUBSCRIBE)
		{
			//$actions[Weblcms :: PARAM_UNSUBSCRIBE_SELECTED] = Translation :: get('UnsubscribeSelected');
		}
		else
		{
			//$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT] = Translation :: get('SubscribeSelectedAsStudent');
			//$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN] = Translation :: get('SubscribeSelectedAsAdmin');
		}

		if ($browser->get_course()->is_course_admin($browser->get_user()))
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>