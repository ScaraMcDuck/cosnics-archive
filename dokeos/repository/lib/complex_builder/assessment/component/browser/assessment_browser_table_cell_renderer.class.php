<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 15489 2008-05-29 07:53:34Z Scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/assessment_browser_table_column_model.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class AssessmentBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function AssessmentBrowserTableCellRenderer($browser, $condition)
	{
		parent :: __construct($browser, $condition);
	}
	// Inherited
	function render_cell($column, $cloi)
	{
		$return = parent :: render_cell($column, $cloi);
		if($return != '')
			return $return;
		
		switch ($column->get_title())
		{ 
			case Translation :: get('Weight'): return $cloi->get_weight();
		}
		
		return '';
	}
}
?>