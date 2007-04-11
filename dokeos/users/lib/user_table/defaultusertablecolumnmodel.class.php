<?php
/**
 * @package repository.usertable
 */
require_once dirname(__FILE__).'/usertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/usertablecolumn.class.php';
require_once dirname(__FILE__).'/../user.class.php';

/**
 * TODO: Add comment
 */
class DefaultUserTableColumnModel extends UserTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultUserTableColumnModel()
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
		$columns[] = new UserTableColumn(User :: PROPERTY_LASTNAME, true);
		$columns[] = new UserTableColumn(User :: PROPERTY_FIRSTNAME, true);
		return $columns;
	}
}
?>