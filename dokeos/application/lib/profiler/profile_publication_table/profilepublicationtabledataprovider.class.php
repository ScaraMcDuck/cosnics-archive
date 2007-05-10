<?php
/**
 * @package application.lib.profiler.profile_publication_table
 */
interface ProfilePublicationTableDataProvider
{
    function get_profile_publications($offset, $count, $order_property, $order_direction);

    function get_profile_publication_count();
}
?>