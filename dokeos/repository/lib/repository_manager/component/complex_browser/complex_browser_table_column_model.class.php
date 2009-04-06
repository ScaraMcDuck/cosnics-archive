<?php
/**
 * $Id: repository_browser_table_column_model.class.php 15472 2008-05-27 18:47:47Z Scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
/**
 * Table column model for the repository browser table
 */
class ComplexBrowserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function ComplexBrowserTableColumnModel($show_subitems_column)
	{
		parent :: __construct(self :: get_default_columns($show_subitems_column), 1);
		$this->set_default_order_column(0);
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
	
	private static function get_default_columns($show_subitems_column = true)
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_TYPE);
		$columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION);
		//$columns[] = new ObjectTableColumn(ComplexLearningObjectItem :: PROPERTY_DISPLAY_ORDER);
		if($show_subitems_column)
			$columns[] = new ObjectTableColumn('subitems');
		//$columns[] = new ObjectTableColumn(LearningObject :: PROPERTY_MODIFICATION_DATE);
		$columns[] = self :: get_modification_column();
		return $columns;
	}
}
?>
