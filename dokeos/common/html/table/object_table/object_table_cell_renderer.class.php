<?php
/**
 * @package common.html.table.common
 */
/**
 * 
 * TODO: Add comment
 * 
 */
interface ObjectTableCellRenderer
{
	/**
	 * TODO: Add comment
	 */
	function render_cell($column, $object);
	
	function render_id_cell($object);
}
?>