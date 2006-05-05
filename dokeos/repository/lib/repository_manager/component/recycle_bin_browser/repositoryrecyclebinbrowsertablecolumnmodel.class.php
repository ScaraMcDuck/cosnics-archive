<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttablecolumn.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';

class RepositoryRecycleBinBrowserTableColumnModel extends DefaultLearningObjectTableColumnModel
{
	private static $restore_column;
	
	function RepositoryRecycleBinBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$col = new LearningObjectTableColumn(LearningObject :: PROPERTY_PARENT_ID, true);
		$col->set_title(get_lang('OriginalLocation'));
		$this->add_column($col);
		$this->add_column(self :: get_restore_column());
	}

	static function get_restore_column()
	{
		if (!isset(self :: $restore_column))
		{
			self :: $restore_column = new LearningObjectTableColumn(get_lang('Restore'));
		}
		return self :: $restore_column;
	}
}
?>
