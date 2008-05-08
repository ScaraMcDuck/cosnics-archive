<?php
/**
 * $Id:$
 * @package application.weblcms.weblcms_manager.component
 */
require_once Path :: get_user_path(). 'lib/user_table/usertable.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertablecellrenderer.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';
/**
 * Table to display a list of users not subscribed to a course.
 */
class SubscribedUserBrowserTable extends UserTable
{
	/**
	 * Constructor
	 */
	function SubscribedUserBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new SubscribedUserBrowserTableColumnModel();
		$renderer = new SubscribedUserBrowserTableCellRenderer($browser);
		$data_provider = new SubscribedUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		if ($_GET[Weblcms :: PARAM_USER_ACTION] != Weblcms :: ACTION_SUBSCRIBE)
		{
			$actions[Weblcms :: PARAM_UNSUBSCRIBE_SELECTED] = Translation :: get('UnsubscribeSelected');
		}
		else
		{
			$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT] = Translation :: get('SubscribeSelectedAsStudent');
			$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN] = Translation :: get('SubscribeSelectedAsAdmin');
		}
		$actions[UserTool::USER_DETAILS] = Translation :: get('Details');
		if ($browser->get_course()->is_course_admin($browser->get_user()))
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>