<?php
/**
 * $Id: grouptool.class.php 12541 2007-06-06 07:34:34Z bmol $
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/grouptablecellrenderer.class.php';

class DefaultGroupTableCellRenderer implements GroupTableCellRenderer
{
	private $group_tool;
	/**
	 * Constructor
	 */
	function DefaultGroupTableCellRenderer($group_tool)
	{
		$this->group_tool = $group_tool;
	}
	/**
	 * Renders a table cell
	 * @param GroupTableColumnModel $column The column which should be rendered
	 * @param Group $group The User to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $group)
	{
		if ($property = $column->get_group_property())
		{
			switch ($property)
			{
				case Group :: PROPERTY_ID :
					return $group->get_id();
				case Group :: PROPERTY_NAME :
					$url = $this->group_tool->get_url(array (Weblcms :: PARAM_GROUP => $group->get_id()));
					return '<a href="'.$url.'">'.$group->get_name().'</a>';
				case Group :: PROPERTY_DESCRIPTION :
					return $group->get_description();
				case Group :: PROPERTY_MAX_NUMBER_OF_MEMBERS :
					return $group->get_max_number_of_members();
				case Group :: PROPERTY_SELF_REG :
					return $group->is_self_registration_allowed() ? get_lang('Yes') : get_lang('No');
				case Group :: PROPERTY_SELF_UNREG :
					return $group->is_self_unregistration_allowed() ? get_lang('Yes') : get_lang('No');
			}
		}
		return '&nbsp;';
	}
}
?>