<?php
/**
 * @package users.lib.usermanager.component.admin_user_browser
 */
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
/**
 * Table column model for the user browser table
 */
class WhoisOnlineTableColumnModel extends DefaultUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function WhoisOnlineTableColumnModel()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_EMAIL));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_STATUS));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_PICTURE_URI));
        //$this->add_column(new ObjectTableColumn(User :: PROPERTY_PLATFORMADMIN));
        $this->set_default_order_column(1);
    }
}
?>
