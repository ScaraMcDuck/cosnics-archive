<?php
/**
 * @package application.lib.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
interface PmPublicationTableDataProvider
{
    function get_personal_message_publications($offset, $count, $order_property, $order_direction);

    function get_personal_message_publication_count();
}
?>