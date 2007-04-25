<?php
/**
 * @package repository.publicationtable
 */
/**
 * todo: add comment
 */
interface PmPublicationTableDataProvider
{
    function get_personal_message_publications($offset, $count, $order_property, $order_direction);

    function get_personal_message_publication_count();
}
?>