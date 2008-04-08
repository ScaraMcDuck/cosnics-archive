<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage publisher
 */
require_once Path :: get_repository_path(). 'lib/learning_object_table/learningobjecttablecolumnmodel.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_table/learningobjecttablecolumn.class.php';
require_once Path :: get_repository_path(). 'lib/learningobject.class.php';
/**
 * This class represents a column model for a publication candidate table
 */
class PublicationCandidateTableColumnModel extends LearningObjectTableColumnModel {
	/**
	 * The column with the action buttons.
	 */
	private static $action_column;
	/**
	 * Constructor.
	 */
	function PublicationCandidateTableColumnModel()
	{
		parent :: __construct(self :: get_columns(), 1, SORT_ASC);
	}
	/**
	 * Gets the columns of this table.
	 * @return array An array of all columns in this table.
	 * @see LearningObjectTableColumn
	 */
	private static function get_columns()
	{
		$columns = array();
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TYPE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TITLE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true);
		$columns[] = self :: get_action_column();
		return $columns;
	}
	/**
	 * Gets the column wich contains the action buttons.
	 * @return LearningObjectTableColumn The action column.
	 */
	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new LearningObjectTableColumn(Translation :: get('Publish'));
		}
		return self :: $action_column;
	}
}
?>