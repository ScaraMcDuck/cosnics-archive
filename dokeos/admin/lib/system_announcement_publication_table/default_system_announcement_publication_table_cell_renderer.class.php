<?php
/**
 * @package application.lib.profiler.profile_publication_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../system_announcement_publication.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';

class DefaultSystemAnnouncementPublicationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSystemAnnouncementPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ProfileTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $profile_publication The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $profile_publication)
    {
        if ($property = $column->get_object_property())
        {
            $user = $profile_publication->get_publication_publisher();
            switch ($property)
            {
                case SystemAnnouncementPublication :: PROPERTY_LEARNING_OBJECT_ID :
                    return $profile_publication->get_publication_object()->get_title();
            }
        }
        return '&nbsp;';
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>