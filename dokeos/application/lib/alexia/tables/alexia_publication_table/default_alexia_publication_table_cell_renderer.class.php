<?php
/**
 * @package application.lib.alexiar.alexia_publication_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object.class.php';
require_once dirname(__FILE__) . '/../../alexia_publication.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';

class DefaultAlexiaPublicationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultAlexiaPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param AlexiaTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $alexia_publication The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $alexia_publication)
    {
        $content_object = $alexia_publication->get_publication_object();
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                return DokeosUtilities :: truncate_string($content_object->get_description(), 200);
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>