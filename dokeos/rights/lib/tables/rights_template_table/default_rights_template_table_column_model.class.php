<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../rights_template.class.php';

/**
 * TODO: Add comment
 */
class DefaultRightsTemplateTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultRightsTemplateTableColumnModel()
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
		$columns[] = new ObjectTableColumn(RightsTemplate :: PROPERTY_NAME);
		$columns[] = new ObjectTableColumn(RightsTemplate :: PROPERTY_DESCRIPTION);
		return $columns;
	}
}
?>