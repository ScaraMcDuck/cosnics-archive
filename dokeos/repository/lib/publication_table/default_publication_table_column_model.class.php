<?php
/**
 * @package repository.publicationtable
 */
require_once dirname(__FILE__).'/publication_table_column_model.class.php';
require_once dirname(__FILE__).'/publication_table_column.class.php';
require_once dirname(__FILE__).'/../learning_object.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_attributes.class.php';

/**
 * TODO: Add comment
 */
class DefaultPublicationTableColumnModel extends PublicationTableColumnModel
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
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new PublicationTableColumn(LearningObjectPublicationAttributes :: PROPERTY_APPLICATION, true);
		$columns[] = new PublicationTableColumn(LearningObjectPublicationAttributes :: PROPERTY_LOCATION, true);
		$columns[] = new PublicationTableColumn(LearningObject :: PROPERTY_TITLE, true);
		$columns[] = new PublicationTableColumn(LearningObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE, true);
		return $columns;
	}
}
?>