<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/../../../../../../users/lib/user_table/usertable.class.php';
require_once dirname(__FILE__).'/groupunsubscribeduserbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/groupunsubscribeduserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/groupunsubscribeduserbrowsertablecellrenderer.class.php';
class GroupUnsubscribedUserBrowserTable extends UserTable
{
	/**
	 * Constructor
	 */
	function GroupUnsubscribedUserBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new GroupUnsubscribedUserBrowserTableColumnModel();
		$renderer = new GroupUnsubscribedUserBrowserTableCellRenderer($browser);
		$data_provider = new GroupUnsubscribedUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		if ($_GET[Weblcms :: PARAM_USER_ACTION] != Weblcms :: ACTION_SUBSCRIBE)
		{
			//$actions[Weblcms :: PARAM_UNSUBSCRIBE_SELECTED] = get_lang('UnsubscribeSelected');
		}
		else
		{
			//$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT] = get_lang('SubscribeSelectedAsStudent');
			//$actions[Weblcms :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN] = get_lang('SubscribeSelectedAsAdmin');
		}

		if ($browser->get_course()->is_course_admin($browser->get_user_id()))
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>