<?php
/**
 * @package application.lib.profiler.profile_publication_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object.class.php';
require_once dirname(__FILE__) . '/../profile_publication.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';

class DefaultProfilePublicationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultProfilePublicationTableCellRenderer()
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
                case ProfilePublication :: PROPERTY_PROFILE :
                    return $profile_publication->get_publication_object()->get_title();
                case User :: PROPERTY_USERNAME :
                    return $user->get_username();
                case User :: PROPERTY_LASTNAME :
                    return $user->get_lastname();
                case User :: PROPERTY_FIRSTNAME :
                    return $user->get_firstname();
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