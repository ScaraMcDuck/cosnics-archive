<?php
/**
 * @package webconferencing.tables.webconference_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../webconference.class.php';

/**
 * Default column model for the webconference table
 * @author Stefaan Vanbillemont
 */
class DefaultWebconferenceTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultWebconferenceTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(Webconference :: PROPERTY_CONFNAME, true);
		$columns[] = new ObjectTableColumn(Webconference :: PROPERTY_DESCRIPTION, true);
		$columns[] = new ObjectTableColumn(Webconference :: PROPERTY_DURATION, true);

		return $columns;
	}
}
?>