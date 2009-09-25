<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
require_once dirname(__FILE__) . '/../../content_object_publication.class.php';
/**
 * This class represents a column model for a publication candidate table
 */
class ObjectPublicationTableColumnModel extends ObjectTableColumnModel 
{
	/**
	 * The column with the action buttons.
	 */
	private static $action_column;
	/**
	 * Constructor.
	 */
	function ObjectPublicationTableColumnModel($columns)
	{
		if($columns)
		{
			parent :: __construct($columns, 1, SORT_ASC);
		}
		else 
		{
			parent :: __construct(self :: get_columns(), 1, SORT_ASC);		
		}
	
	}
	/**
	 * Gets the columns of this table.
	 * @return array An array of all columns in this table.
	 * @see ContentObjectTableColumn
	 */
	function get_columns()
	{
		$columns = $this->get_basic_columns();
		$columns[] = self :: get_action_column();
		return $columns;
	}
	
	function get_basic_columns()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		
		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true, $wdm->get_alias(ContentObject :: get_table_name()));
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true, $wdm->get_alias(ContentObject :: get_table_name()));
		$columns[] = new ObjectTableColumn(ContentObjectPublication :: PROPERTY_PUBLICATION_DATE, true, $wdm->get_alias(ContentObjectPublication :: get_table_name()));
		$columns[] = new ObjectTableColumn(ContentObjectPublication :: PROPERTY_PUBLISHER_ID, true, $wdm->get_alias(ContentObjectPublication :: get_table_name()));
		$columns[] = new ObjectTableColumn('published_for', false);
		return $columns;
	}
	
	/**
	 * Gets the column wich contains the action buttons.
	 * @return ContentObjectTableColumn The action column.
	 */
	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new StaticTableColumn(Translation :: get('Actions'));
		}
		return self :: $action_column;
	}
}
?>