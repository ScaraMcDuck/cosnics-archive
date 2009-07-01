<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_admin_path() . 'lib/tables/remote_package_table/default_remote_package_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/remote_package.class.php';
/**
 * Table column model for the user browser table
 */
class RemotePackageBrowserTableColumnModel extends DefaultRemotePackageTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function RemotePackageBrowserTableColumnModel()
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
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
