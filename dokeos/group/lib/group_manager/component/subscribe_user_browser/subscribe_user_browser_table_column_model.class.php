<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once Path :: get_user_path(). 'lib/user_table/default_user_table_column_model.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
/**
 * Table column model for the user browser table
 */
class SubscribeUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function SubscribeUserBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->add_column(new ObjectTableColumn(User :: PROPERTY_USERNAME, true));
		$this->add_column(new ObjectTableColumn(User :: PROPERTY_EMAIL, true));
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
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
