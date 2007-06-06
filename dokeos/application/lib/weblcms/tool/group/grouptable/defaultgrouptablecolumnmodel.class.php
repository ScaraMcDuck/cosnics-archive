<?php
/**
 * $Id: grouptool.class.php 12541 2007-06-06 07:34:34Z bmol $
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/grouptablecolumnmodel.class.php';
require_once dirname(__FILE__).'/grouptablecolumn.class.php';

class DefaultGroupTableColumnModel extends GroupTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultGroupTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return UserTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new GroupTableColumn(Group :: PROPERTY_NAME, true);
		$columns[] = new GroupTableColumn(Group :: PROPERTY_DESCRIPTION, true);
		$columns[] = new GroupTableColumn(Group :: PROPERTY_MAX_NUMBER_OF_MEMBERS, true);
		$columns[] = new GroupTableColumn(Group :: PROPERTY_SELF_UNREG, true);
		$columns[] = new GroupTableColumn(Group :: PROPERTY_SELF_REG, true);
		return $columns;
	}
}
?>