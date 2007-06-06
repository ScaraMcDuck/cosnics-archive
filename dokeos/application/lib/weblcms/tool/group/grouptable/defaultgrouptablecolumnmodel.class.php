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
	 * The tables modification column
	 */
	private static $modification_column;
   	/**
	 * The tables number of members column
	 */
	private static $number_of_members_column;
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
		$columns[] = self :: get_number_of_members_column();
		$columns[] = new GroupTableColumn(Group :: PROPERTY_MAX_NUMBER_OF_MEMBERS, true);
		$columns[] = new GroupTableColumn(Group :: PROPERTY_SELF_UNREG, true);
		$columns[] = new GroupTableColumn(Group :: PROPERTY_SELF_REG, true);
		$columns[] = self :: get_modification_column();
		return $columns;
	}
	/**
	 * Gets the modification column
	 * @return GroupTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new GroupTableColumn('');
		}
		return self :: $modification_column;
	}
	/**
	 * Gets the number of members column
	 * @return GroupTableColumn
	 */
	static function get_number_of_members_column()
	{
		if (!isset(self :: $number_of_members_column))
		{
			self :: $number_of_members_column = new GroupTableColumn(get_lang('NumberOfMembers'), false);
		}
		return self :: $number_of_members_column;
	}
}
?>