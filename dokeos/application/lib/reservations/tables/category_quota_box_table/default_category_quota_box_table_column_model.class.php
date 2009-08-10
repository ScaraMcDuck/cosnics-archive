<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../quota_box.class.php';
require_once dirname(__FILE__).'/../../quota_box_rel_category.class.php';


/**
 * TODO: Add comment
 */
class DefaultCategoryQuotaBoxTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCategoryQuotaBoxTableColumnModel()
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
		$columns[] = new ObjectTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(QuotaBox :: PROPERTY_NAME)), false);
		$columns[] = new ObjectTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(QuotaBox :: PROPERTY_DESCRIPTION)), false);
		return $columns;
	}
}
?>