<?php
require_once dirname(__FILE__).'/learningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/learningobjecttablecolumn.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';

class DefaultLearningObjectTableColumnModel extends LearningObjectTableColumnModel {
	function DefaultLearningObjectTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TYPE, true); 
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TITLE, true); 
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true); 
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_MODIFICATION_DATE, true); 
		return $columns;
	}
}
?>