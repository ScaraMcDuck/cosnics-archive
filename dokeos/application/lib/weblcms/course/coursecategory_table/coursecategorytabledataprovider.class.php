<?php
/**
 * @package repository.coursecategorytable
 */
/**
 * todo: add comment
 */
interface CourseCategoryTableDataProvider
{
    function get_course_categories($offset, $count, $order_property, $order_direction);

    function get_course_category_count();
}
?>