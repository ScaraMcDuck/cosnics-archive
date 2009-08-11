<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../quota_box.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * TODO: Add comment
 */
class DefaultQuotaBoxTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultQuotaBoxTableCellRenderer($browser)
	{
		
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $quota_box)
	{
		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case QuotaBox :: PROPERTY_NAME :
					return $quota_box->get_name();
				case QuotaBox :: PROPERTY_DESCRIPTION :
					return strip_tags($quota_box->get_description());
				/*case QuotaBox :: PROPERTY_ID :
					return $quota_box->get_id();
				case QuotaBox :: PROPERTY_CREDITS :
					return $quota_box->get_credits();
				case QuotaBox :: PROPERTY_TIME_UNIT :
					return $quota_box->get_time_unit() . ' ' . Translation :: get('day(s)');*/
			}

		}
			
		return '&nbsp;';
	}
	
	function render_id_cell($quota_box)
	{
		return $quota_box->get_id();
	}
}
?>