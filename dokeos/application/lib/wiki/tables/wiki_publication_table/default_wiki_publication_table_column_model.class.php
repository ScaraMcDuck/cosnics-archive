<?php
/**
 * @package wiki.tables.wiki_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../wiki_publication.class.php';

/**
 * Default column model for the wiki_publication table
 * @author Sven Vanpoucke & Stefan Billiet
 */
class DefaultWikiPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultWikiPublicationTableColumnModel()
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

//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_ID, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_LEARNING_OBJECT, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_PARENT_ID, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_CATEGORY, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_FROM_DATE, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_TO_DATE, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_HIDDEN, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_PUBLISHER, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_PUBLISHED, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_MODIFIED, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_DISPLAY_ORDER, true);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_EMAIL_SENT, true);

        $columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_TITLE, true);
        $columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true);

		return $columns;
	}
}
?>