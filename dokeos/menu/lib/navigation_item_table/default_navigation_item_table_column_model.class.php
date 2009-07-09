<?php
/**
 * @package application.lib.menu.menu_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__) . '/../navigation_item.class.php';

class DefaultNavigationItemTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultNavigationItemTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return MenuManagerTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new StaticTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(NavigationItem :: PROPERTY_TITLE)));
        return $columns;
    }
}
?>