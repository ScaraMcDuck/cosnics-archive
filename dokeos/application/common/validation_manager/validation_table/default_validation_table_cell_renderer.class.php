<?php


require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object.class.php';
//require_once dirname(__FILE__) . '/../../../lib/profiler/profile_publication.class.php';
require_once Path::get_admin_path().'lib/validation.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';

class DefaultValidationTableCellRend implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultValidationTableCellRend()
    {
    }

    /**
     * Renders a table cell
     * @param ValidationTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $validation The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $validation)
    {
        $user = $validation->get_validation_publisher();
        switch ($column->get_name())
        {
            //$profile_publication->get_publication_object()->get_title();
            case User :: PROPERTY_USERNAME :
                return $user->get_username();
            case User :: PROPERTY_LASTNAME :
                return $user->get_lastname();
            case User :: PROPERTY_FIRSTNAME :
                return $user->get_firstname();
            case Validation :: PROPERTY_VALIDATED :
                return $validation->get_validated();
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