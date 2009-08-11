<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../quota.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * TODO: Add comment
 */
class DefaultQuotaTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultQuotaTableCellRenderer($browser)
	{
		
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $quota)
	{
		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case Quota :: PROPERTY_ID :
					return $quota->get_id();
				case Quota :: PROPERTY_CREDITS :
					return $quota->get_credits();
				case Quota :: PROPERTY_TIME_UNIT :
					return $quota->get_time_unit() . ' ' . Translation :: get('day(s)');
			}

		}
			
		return '&nbsp;';
	}
	
	function render_id_cell($quota)
	{
		return $quota->get_id();
	}
}
?>