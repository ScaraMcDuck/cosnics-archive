<?php
/**
 * @package repository.usertable
 */
/**
 * todo: add comment
 */
interface UserTableDataProvider
{
    function get_users($user = null, $category = null, $offset, $count, $order_property, $order_direction);

    function get_user_count();
}
?>