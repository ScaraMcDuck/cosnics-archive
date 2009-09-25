<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../content_object_table/default_content_object_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class RepositoryBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function RepositoryBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$this->add_column(new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE));
		$this->add_column(new StaticTableColumn(Translation :: get('Versions')));
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return ContentObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
