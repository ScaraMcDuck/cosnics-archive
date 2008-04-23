<?php
/**
 * @package users.lib.usermanager.component.adminuserbrowser
 */
require_once dirname(__FILE__).'/../../../user_table/defaultusertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../user.class.php';
/**
 * Table column model for the user browser table
 */
class AdminUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function AdminUserBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->add_column(new UserTableColumn(User :: PROPERTY_USERNAME, true));
		$this->add_column(new UserTableColumn(User :: PROPERTY_EMAIL, true));
		$this->add_column(new UserTableColumn(User :: PROPERTY_STATUS, true));
		$this->add_column(new UserTableColumn(User :: PROPERTY_PLATFORMADMIN, true));
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new UserTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
