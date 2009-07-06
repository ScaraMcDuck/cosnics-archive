<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_admin_path() . 'lib/tables/registration_table/default_registration_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/registration.class.php';
/**
 * Table column model for the user browser table
 */
class RegistrationBrowserTableColumnModel extends DefaultRegistrationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function RegistrationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return LearningObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new ObjectTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
