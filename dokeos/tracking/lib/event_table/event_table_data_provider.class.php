<?php
/**
 * @package repository.usertable
 */
/**
 * todo: add comment
 */
interface EventTableDataProvider
{

    function get_events($offset, $count, $order_property, $order_direction);

    function get_event_count();
}
?>