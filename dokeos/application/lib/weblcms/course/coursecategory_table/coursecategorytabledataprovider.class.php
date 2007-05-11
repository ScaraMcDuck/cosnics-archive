<?php
/**
 * @package application.lib.weblcms.course.coursecategory_table
 */
interface CourseCategoryTableDataProvider
{
    function get_course_categories($offset, $count, $order_property, $order_direction);

    function get_course_category_count();
}
?>