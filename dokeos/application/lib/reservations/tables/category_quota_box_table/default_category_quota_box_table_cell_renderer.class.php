<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../quota_box.class.php';
require_once dirname(__FILE__).'/../../quota_box_rel_category.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * TODO: Add comment
 */
class DefaultCategoryQuotaBoxTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultCategoryQuotaBoxTableCellRenderer($browser)
	{
		
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	
	private $qb;
	
	function render_cell($column, $quota_box_rel_category)
	{
		if ($title = $column->get_title())
		{
			$name = Translation :: get(DokeosUtilities :: underscores_to_camelcase(QuotaBox :: PROPERTY_NAME));
			$description = Translation :: get(DokeosUtilities :: underscores_to_camelcase(QuotaBox :: PROPERTY_DESCRIPTION));
			
			$qb = $this->qb;
			if(!$qb || $qb->get_id() != $quota_box_rel_category->get_quota_box_id())
			{
				$qb = $this->browser->retrieve_quota_boxes(new EqualityCondition(QuotaBox :: PROPERTY_ID, $quota_box_rel_category->get_quota_box_id()))->next_result();
				$this->qb = $qb;
			}
			
			switch ($title)
			{
				case $name :
					return $qb->get_name();
				case $description :
					return strip_tags($qb->get_description());
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
}
?>