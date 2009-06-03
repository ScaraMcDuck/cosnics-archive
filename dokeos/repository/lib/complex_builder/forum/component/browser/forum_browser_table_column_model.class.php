<?php
/**
 * $Id: repository_browser_table_column_model.class.php 15472 2008-05-27 18:47:47Z Scara84 $
 * @package repository.repositorymanager
 */

/**
 * Table column model for the repository browser table
 */
class ForumBrowserTableColumnModel extends ComplexBrowserTableColumnModel
{
	/**
	 * Constructor
	 */
	function ForumBrowserTableColumnModel($show_subitems_column)
	{
		$columns[] = new ObjectTableColumn(Translation :: get('AddDate'), false);
		parent :: __construct($show_subitems_column, $columns);
	}
}
?>
