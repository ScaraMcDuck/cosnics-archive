<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../webservice_table/default_webservice_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../webservice_registration.class.php';
/**
 * Table column model for the user browser table
 */
class WebserviceBrowserTableColumnModel extends DefaultWebserviceTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function WebserviceBrowserTableColumnModel()
	{
		parent :: __construct();		
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
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
