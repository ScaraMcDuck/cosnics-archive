<?php
/**
 * @package repository.publicationtable
 * 
 * TODO: Add comment
 */
interface PublicationTableDataProvider
{
    function get_learning_object_publication_attributes($offset, $count, $order_property, $order_direction);

    function get_learning_object_publication_count();
}
?>