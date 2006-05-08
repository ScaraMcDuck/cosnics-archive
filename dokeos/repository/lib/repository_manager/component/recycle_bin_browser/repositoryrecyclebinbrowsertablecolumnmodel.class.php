<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttablecolumn.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';

class RepositoryRecycleBinBrowserTableColumnModel extends DefaultLearningObjectTableColumnModel
{
	private static $action_column;

	function RepositoryRecycleBinBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$col = new LearningObjectTableColumn(LearningObject :: PROPERTY_PARENT_ID, true);
		$col->set_title(get_lang('OriginalLocation'));
		$this->add_column($col);
		$this->add_column(self :: get_action_column());
	}

	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new LearningObjectTableColumn('');
		}
		return self :: $action_column;
	}
}
?>
