<?php
/**
 * $Id: grouptool.class.php 12541 2007-06-06 07:34:34Z bmol $
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
interface GroupTableCellRenderer
{
	function render_cell($column, $learning_object);
}
?>