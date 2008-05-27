<?php
/**
 * @package common.html.table.common
 */
/**
 * todo: add comment
 */
interface ObjectTableDataProvider
{
    function get_objects($offset, $count, $order_property = null, $order_direction = null);

    function get_object_count();
}
?>