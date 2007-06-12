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
		if ($column === DefaultGroupTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($group);
		}
		if ($column === DefaultGroupTableColumnModel :: get_number_of_members_column())
		{
			if (!is_null($group->get_members()))
			{
				return $group->get_members()->size();
			}
			else
			{
				return '0';
			}
		}
		if ($property = $column->get_group_property())
		{
			switch ($property)
			{
				case Group :: PROPERTY_ID :
					return $group->get_id();
				case Group :: PROPERTY_NAME :
					if($this->group_tool->is_allowed(EDIT_RIGHT) || $group->is_member($this->group_tool->get_user()) )
					{
						$url = $this->group_tool->get_url(array (Weblcms :: PARAM_GROUP => $group->get_id()));
						return '<a href="'.$url.'">'.$group->get_name().'</a>';
					}
					else
					{
						return $group->get_name();
					}
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
	/**
	 * Gets the action links to display
	 * @param Group $group The group for which the action links should be
	 * returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($group)
	{
		$toolbar_data = array ();
		$parameters = array ();
		$parameters[Weblcms :: PARAM_GROUP] = $group->get_id();
		$details_url = $this->group_tool->get_url($parameters);
		$toolbar_data[] = array ('href' => $details_url, 'label' => get_lang('Details'), 'img' => api_get_path(WEB_CODE_PATH).'/img/description.gif');
		$parameters = array ();
		$parameters[Weblcms :: PARAM_GROUP] = $group->get_id();
		$delete_url = $this->group_tool->get_url($parameters);
		$toolbar_data[] = array ('href' => $details_url, 'label' => get_lang('Delete'), 'img' => api_get_path(WEB_CODE_PATH).'/img/delete.gif');
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>