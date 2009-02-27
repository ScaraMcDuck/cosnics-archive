<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../webservice_registration.class.php';

/**
 * TODO: Add comment
 */
class DefaultWebserviceTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultWebserviceTableColumnModel()
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
		$columns[] = new ObjectTableColumn(WebserviceRegistration :: PROPERTY_NAME, true);
		$columns[] = new ObjectTableColumn(WebserviceRegistration :: PROPERTY_DESCRIPTION, true);
		return $columns;
	}
}
?>