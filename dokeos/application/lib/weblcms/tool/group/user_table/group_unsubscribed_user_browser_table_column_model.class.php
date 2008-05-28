<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
 
require_once Path :: get_user_path(). 'lib/user_table/default_user_table_column_model.class.php';
 
class GroupUnsubscribedUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
   	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function GroupUnsubscribedUserBrowserTableColumnModel()
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