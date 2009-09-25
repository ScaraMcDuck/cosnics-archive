<?php
/**
 * @package repository.publicationtable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../content_object.class.php';
require_once dirname(__FILE__).'/../content_object_publication_attributes.class.php';

/**
 * TODO: Add comment
 */
class DefaultPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultPublicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 3);
	}
	/**
	 * Gets the default columns for this model
	 * @return ContentObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_APPLICATION);
		$columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_LOCATION);
		$columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE);
		return $columns;
	}
}
?>