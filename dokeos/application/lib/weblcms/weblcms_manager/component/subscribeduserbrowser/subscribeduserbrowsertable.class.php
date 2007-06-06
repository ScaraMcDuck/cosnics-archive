<?php
/**
 * $Id:$
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../../../../users/lib/user_table/usertable.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../../../users/lib/usermanager/usermanager.class.php';
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
			$actions[Weblcms :: PARAM_UNSUBSCRIBE_SELECTED] = get_lang('UnsubscribeSelected');
		}
		else
		{
			$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT] = get_lang('SubscribeSelectedAsStudent');
			$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN] = get_lang('SubscribeSelectedAsAdmin');
		}
		$actions[UserTool::USER_DETAILS] = get_lang('Details');
		if ($browser->get_course()->is_course_admin($browser->get_user_id()))
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>