<?php
/**
 * @package repository.courseusercategorytable
 */
/**
 * todo: add comment
 */
interface CourseUserCategoryTableDataProvider
{
    function get_course_user_categories($offset, $count, $order_property, $order_direction);

    function  get_course_user_category_count();
}
?>