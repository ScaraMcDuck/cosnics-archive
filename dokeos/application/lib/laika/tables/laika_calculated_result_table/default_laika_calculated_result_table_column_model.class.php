<?php
/**
 * @package repository.publicationtable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../laika_calculated_result.class.php';

/**
 * TODO: Add comment
 */
class DefaultLaikaCalculatedResultTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultLaikaCalculatedResultTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$user_alias = UserDataManager :: get_instance()->get_database()->get_alias(User :: get_table_name());
		$attempt_alias = LaikaDataManager :: get_instance()->get_database()->get_alias(LaikaAttempt :: get_table_name());
		
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_EMAIL, true, $user_alias);
		$columns[] = new ObjectTableColumn(LaikaAttempt :: PROPERTY_DATE, true, $attempt_alias);
		return $columns;
	}
}
?>