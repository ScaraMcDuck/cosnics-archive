<?php
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object_table/learningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object_table/learningobjecttablecolumn.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learningobject.class.php';

class PublicationCandidateTableColumnModel extends LearningObjectTableColumnModel {
	private static $action_column;
	
	function PublicationCandidateTableColumnModel()
	{
		parent :: __construct(self :: get_columns(), 1, SORT_ASC);
	}
	
	private static function get_columns()
	{
		$columns = array();
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TYPE, true); 
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TITLE, true); 
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true);
		$columns[] = self :: get_action_column(); 
		return $columns;
	}

	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new LearningObjectTableColumn(get_lang('Publish'));
		}
		return self :: $action_column;
	}
}
?>