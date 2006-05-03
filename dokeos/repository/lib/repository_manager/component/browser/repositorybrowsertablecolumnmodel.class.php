<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecolumnmodel.class.php';

class RepositoryBrowserTableColumnModel extends DefaultLearningObjectTableColumnModel
{
	private static $modification_column;

	function RepositoryBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->add_column(self :: get_modification_column());
	}

	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new LearningObjectTableColumn(get_lang('Modify'));
		}
		return self :: $modification_column;
	}
}
?>
