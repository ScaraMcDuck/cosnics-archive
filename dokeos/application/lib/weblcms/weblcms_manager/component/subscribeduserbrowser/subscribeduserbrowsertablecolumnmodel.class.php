<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../../../../users/lib/user_table/defaultusertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../../../../users/lib/user.class.php';
/**
 * Table column model for the user browser table
 */
class SubscribedUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function SubscribedUserBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->add_column(new UserTableColumn(User :: PROPERTY_USERNAME, true));
		$this->add_column(new UserTableColumn(User :: PROPERTY_EMAIL, true));
		if ($_GET[Weblcms :: PARAM_USER_ACTION] != Weblcms :: ACTION_SUBSCRIBE)
		{
			$this->add_column(new UserTableColumn(User :: PROPERTY_STATUS, true));
		}
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
