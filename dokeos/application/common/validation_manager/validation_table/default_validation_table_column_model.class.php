<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
//require_once dirname(__FILE__) . '/../../../lib/profiler/profile_publication.class.php';
require_once Path::get_admin_path().'lib/validation.class.php';

class DefaultValidationTableColumnMod extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultValidationTableColumnMod()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ValidationTableColumn[]
     */
    private static function get_default_columns()
    {
        $udm = UserDataManager :: get_instance();
        $user_alias = $udm->get_database()->get_alias(User :: get_table_name());

        $columns = array();
        
        // TODO: Make this work by refactoring JOIN statements.
        $columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(Validation :: PROPERTY_VALIDATED);
        return $columns;
    }
}
?>