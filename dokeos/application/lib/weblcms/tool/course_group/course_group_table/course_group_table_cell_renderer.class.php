<?php
/**
 * $Id: course_grouptool.class.php 12541 2007-06-06 07:34:34Z bmol $
 * CourseGroup tool
 * @package application.weblcms.tool
 * @subpackage course_group
 */
interface CourseGroupTableCellRenderer
{
	function render_cell($column, $learning_object);
}
?>