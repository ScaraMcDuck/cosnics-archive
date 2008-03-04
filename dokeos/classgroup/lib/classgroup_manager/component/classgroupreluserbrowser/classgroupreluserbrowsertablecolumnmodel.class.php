<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../classgroup_rel_user_table/defaultclassgrouprelusertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../classgroup.class.php';
/**
 * Table column model for the user browser table
 */
class ClassGroupRelUserBrowserTableColumnModel extends DefaultClassGroupRelUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function ClassGroupRelUserBrowserTableColumnModel()
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
			self :: $modification_column = new ClassGroupRelUserTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
