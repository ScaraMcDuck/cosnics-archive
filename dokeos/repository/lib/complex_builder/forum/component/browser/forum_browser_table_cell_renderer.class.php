<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 15489 2008-05-29 07:53:34Z Scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ForumBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ForumBrowserTableCellRenderer($browser, $condition)
	{
		parent :: __construct($browser, $condition);
	}
	// Inherited
	function render_cell($column, $cloi)
	{
		if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($cloi);
		}

		return parent :: render_cell($column, $cloi);
	}
	
	function get_modification_links($cloi)
	{
		return parent :: get_modification_links($cloi, array(), true);
	}
}
?>