<?php
/**
 * @package application.lib.weblcms.course.courseusercategory_table
 */
interface CourseUserCategoryTableDataProvider
{
    function get_course_user_categories($offset, $count, $order_property, $order_direction);

    function  get_course_user_category_count();
}
?>