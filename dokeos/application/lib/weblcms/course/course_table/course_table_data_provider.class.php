<?php
/**
 * @package application.lib.weblcms.course.course_table
 */
interface CourseTableDataProvider
{
    function get_courses($user = null, $category = null, $offset, $count, $order_property, $order_direction);

    function get_course_count();
}
?>