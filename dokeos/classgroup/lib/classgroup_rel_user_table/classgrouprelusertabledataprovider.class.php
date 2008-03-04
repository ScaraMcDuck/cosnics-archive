<?php
/**
 * @package repository.usertable
 */
/**
 * todo: add comment
 */
interface ClassGroupRelUserTableDataProvider
{
    function get_classgroup_rel_users($classgroupreluser = null, $category = null, $offset, $count, $order_property, $order_direction);

    function get_classgroup_rel_user_count();
}
?>