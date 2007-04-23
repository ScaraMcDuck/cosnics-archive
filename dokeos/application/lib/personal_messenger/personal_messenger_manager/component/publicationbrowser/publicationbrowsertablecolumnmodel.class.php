<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../publication_table/defaultpublicationtablecolumnmodel.class.php';
/**
 * Table column model for the publication browser table
 */
class PublicationBrowserTableColumnModel extends DefaultPublicationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function PublicationBrowserTableColumnModel($folder)
	{
		parent :: __construct($folder);
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new PublicationTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
