<?php
/**
 * @package repository.coursetable
 */
/**
 * todo: add comment
 */
interface CourseTableDataProvider
{
    function get_courses($user = null, $category = null, $offset, $count, $order_property, $order_direction);

    function get_course_count();
}
?>