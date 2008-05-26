<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttablecolumn.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';

class LearningPathTableColumnModel extends LearningObjectTableColumnModel {
	function LearningPathTableColumnModel()
	{
		parent :: __construct(self :: get_columns(), 5, SORT_ASC);
	}

	private static function get_columns()
	{
		$columns = array();
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TYPE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TITLE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_CREATION_DATE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_MODIFICATION_DATE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX, true);
		return $columns;
	}
}
?>