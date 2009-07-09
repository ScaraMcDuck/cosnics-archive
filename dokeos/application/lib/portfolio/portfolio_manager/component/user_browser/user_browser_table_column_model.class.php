<?php
/**
 * @package portfolio.lib.portfoliomanager.component.user_browser
 */
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
/**
 * Table column model for the user browser table
 */
class UserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	
	/**
	 * Constructor
	 */
	function UserBrowserTableColumnModel()
	{
		parent :: __construct();
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
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
