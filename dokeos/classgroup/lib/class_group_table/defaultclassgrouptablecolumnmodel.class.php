<?php
/**
 * @package repository.usertable
 */
require_once dirname(__FILE__).'/classgrouptablecolumnmodel.class.php';
require_once dirname(__FILE__).'/classgrouptablecolumn.class.php';
require_once dirname(__FILE__).'/../class_group.class.php';

/**
 * TODO: Add comment
 */
class DefaultClassGroupTableColumnModel extends ClassGroupTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultClassGroupTableColumnModel()
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
		$columns[] = new ClassGroupTableColumn(ClassGroup :: PROPERTY_NAME, true);
		$columns[] = new ClassGroupTableColumn(ClassGroup :: PROPERTY_DESCRIPTION, true);
		return $columns;
	}
}
?>