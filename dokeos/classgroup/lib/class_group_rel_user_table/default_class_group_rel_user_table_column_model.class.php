<?php
/**
 * @package repository.usertable
 */
require_once dirname(__FILE__).'/class_group_rel_user_table_column_model.class.php';
require_once dirname(__FILE__).'/class_group_rel_user_table_column.class.php';
require_once dirname(__FILE__).'/../class_group_rel_user.class.php';

/**
 * TODO: Add comment
 */
class DefaultClassGroupRelUserTableColumnModel extends ClassGroupRelUserTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultClassGroupRelUserTableColumnModel()
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
		$columns[] = new ClassGroupRelUserTableColumn('User', true);
		$columns[] = new ClassGroupRelUserTableColumn('Location', true);
		return $columns;
	}
}
?>