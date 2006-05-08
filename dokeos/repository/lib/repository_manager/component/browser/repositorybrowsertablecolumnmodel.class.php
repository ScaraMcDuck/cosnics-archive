<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecolumnmodel.class.php';

class RepositoryBrowserTableColumnModel extends DefaultLearningObjectTableColumnModel
{
	private static $modification_column;

	function RepositoryBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$this->add_column(self :: get_modification_column());
	}

	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new LearningObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
