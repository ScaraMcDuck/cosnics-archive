<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttablecolumn.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';
/**
 * Table column model for the recycle bin browser table
 */
class RepositoryRecycleBinBrowserTableColumnModel extends DefaultLearningObjectTableColumnModel
{
	/**
	 * Column for the action links
	 */
	private static $action_column;
	/**
	 * Constructor
	 */
	function RepositoryRecycleBinBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$col = new LearningObjectTableColumn(LearningObject :: PROPERTY_PARENT_ID, true);
		$col->set_title(get_lang('OriginalLocation'));
		$this->add_column($col);
		$this->add_column(self :: get_action_column());
	}
	/**
	 * Gets the action column
	 * @return LearningObjectTableColumn
	 */
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
