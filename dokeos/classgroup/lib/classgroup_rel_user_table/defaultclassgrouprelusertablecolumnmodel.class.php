<?php
/**
 * @package repository.usertable
 */
require_once dirname(__FILE__).'/classgrouprelusertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/classgrouprelusertablecolumn.class.php';
require_once dirname(__FILE__).'/../classgroupreluser.class.php';

/**
 * TODO: Add comment
 */
class DefaultClassgroupRelUserTableColumnModel extends ClassgroupRelUserTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultClassgroupRelUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ClassgroupRelUserTableColumn('User', true);
		$columns[] = new ClassgroupRelUserTableColumn('Location', true);
		return $columns;
	}
}
?>