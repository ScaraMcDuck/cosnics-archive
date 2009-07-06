<?php
/**
 * @remote_package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_admin_path() . '/lib/remote_package.class.php';

/**
 * TODO: Add comment
 */
class DefaultRemotePackageTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultRemotePackageTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return LearningObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        //$columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_SECTION, true);
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_VERSION, true);
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>