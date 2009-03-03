<?php
/**
 * $Id: course_grouptool.class.php 12541 2007-06-06 07:34:34Z bmol $
 * CourseGroup tool
 * @package application.weblcms.tool
 * @subpackage course_group
 */
class CourseGroupTableDataProvider
{
	private $course_group_tool;
	function CourseGroupTableDataProvider($course_group_tool)
	{
		$this->course_group_tool = $course_group_tool;
	}
	function get_parent()
	{
		return $this->course_group_tool;
	}
    function get_course_groups($category = null, $offset, $count, $order_property, $order_direction)
    {
		$dm = WeblcmsDataManager :: get_instance();
		$course = $this->course_group_tool->get_parent()->get_course();
		
		$order_property = array($order_property);
		$order_direction = array($order_direction);
		
		return $dm->retrieve_course_groups($course->get_id(),$category, $offset, $count, $order_property, $order_direction);
    }

    function get_course_group_count()
    {
		$dm = WeblcmsDataManager :: get_instance();
		$course = $this->course_group_tool->get_parent()->get_course();
		return $dm->retrieve_course_groups($course->get_id())->size();
    }
}
?>