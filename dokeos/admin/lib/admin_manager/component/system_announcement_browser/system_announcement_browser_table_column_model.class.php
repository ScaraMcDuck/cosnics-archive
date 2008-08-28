<?php
/**
 * @package application.lib.profiler.profiler_manager.component.profilepublicationbrowser
 */
require_once dirname(__FILE__).'/../../../system_announcement_table/default_system_announcement_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class SystemAnnouncementBrowserTableColumnModel extends DefaultSystemAnnouncementTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function SystemAnnouncementBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->set_default_order_direction(SORT_ASC);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return ProfileTableColumn
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
