<?php
/**
 * @package repository.publicationtable
 */
require_once dirname(__FILE__).'/publicationtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/publicationtablecolumn.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationattributes.class.php';

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
		parent :: __construct(self :: get_default_columns(), 1);
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