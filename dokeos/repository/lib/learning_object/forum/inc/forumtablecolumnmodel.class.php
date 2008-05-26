<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learning_object_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/learning_object_table_column.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';

class ForumTableColumnModel extends LearningObjectTableColumnModel {
	function ForumTableColumnModel()
	{
		parent :: __construct(self :: get_columns(), 3, SORT_DESC);
	}

	private static function get_columns()
	{
		$columns = array();
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TYPE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TITLE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_CREATION_DATE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_MODIFICATION_DATE, true);
		return $columns;
	}
}
?>