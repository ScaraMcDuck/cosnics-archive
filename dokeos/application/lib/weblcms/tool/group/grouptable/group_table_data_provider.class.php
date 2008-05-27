<?php
/**
 * $Id: grouptool.class.php 12541 2007-06-06 07:34:34Z bmol $
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
class GroupTableDataProvider
{
	private $group_tool;
	function GroupTableDataProvider($group_tool)
	{
		$this->group_tool = $group_tool;
	}
	function get_parent()
	{
		return $this->group_tool;
	}
    function get_groups($category = null, $offset, $count, $order_property, $order_direction)
    {
		$dm = WeblcmsDataManager :: get_instance();
		$course = $this->group_tool->get_parent()->get_course();
		return $dm->retrieve_groups($course->get_id(),$category, $offset, $count, $order_property, $order_direction);
    }

    function get_group_count()
    {
		$dm = WeblcmsDataManager :: get_instance();
		$course = $this->group_tool->get_parent()->get_course();
		return $dm->retrieve_groups($course->get_id())->size();
    }
}
?>