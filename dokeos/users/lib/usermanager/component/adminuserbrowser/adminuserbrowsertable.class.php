<?php
/**
 * @package users.lib.usermanager.component.adminuserbrowser
 */
require_once dirname(__FILE__).'/../../../user_table/usertable.class.php';
require_once dirname(__FILE__).'/adminuserbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/adminuserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/adminuserbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../usermanager.class.php';
/**
 * Table to display a set of users.
 */
class AdminUserBrowserTable extends UserTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function AdminUserBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new AdminUserBrowserTableColumnModel();
		$renderer = new AdminUserBrowserTableCellRenderer($browser);
		$data_provider = new AdminUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		//Deactivated: What should happen when a user is removed ? Full remove or deactivation of account ?
		//$actions[UserManager :: PARAM_REMOVE_SELECTED] = get_lang('RemoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>