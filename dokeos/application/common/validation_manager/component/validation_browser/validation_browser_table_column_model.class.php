<?php

require_once dirname(__FILE__) . '/../../validation_table/default_validation_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class ValidationBrowserTableColumnMod extends DefaultValidationTableColumnMod
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function ValidationBrowserTableColumnMod()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->set_default_order_direction(SORT_ASC);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ProfileTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
