<?php
/**
 * $Id: repository_browser_table_column_model.class.php 15472 2008-05-27 18:47:47Z Scara84 $
 * @package repository.repositorymanager
 */

/**
 * Table column model for the repository browser table
 */
class AssessmentBrowserTableColumnModel extends ComplexBrowserTableColumnModel
{
	/**
	 * Constructor
	 */
	function AssessmentBrowserTableColumnModel($show_subitems_column)
	{
		$columns[] = new ObjectTableColumn(Translation :: get('Weight'), false);
		parent :: __construct($show_subitems_column, $columns);
	}
}
?>
