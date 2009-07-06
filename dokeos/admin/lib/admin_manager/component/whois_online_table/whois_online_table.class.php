<?php
/**
 * @package users.lib.usermanager.component.whois_online
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__) . '/whois_online_table_data_provider.class.php';
require_once dirname(__FILE__) . '/whois_online_table_column_model.class.php';
require_once dirname(__FILE__) . '/whois_online_table_cell_renderer.class.php';
require_once Path :: get_user_path() . 'lib/user_manager/user_manager.class.php';
/**
 * Table to display a set of users.
 */
class WhoisOnlineTable extends ObjectTable
{
    const DEFAULT_NAME = 'whois_online_table';

    /**
     * Constructor
     * @see LearningObjectTable::LearningObjectTable()
     */
    function WhoisOnlineTable($browser, $parameters, $condition)
    {
        $model = new WhoisOnlineTableColumnModel();
        $renderer = new WhoisOnlineTableCellRenderer($browser);
        $data_provider = new WhoisOnlineTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, WhoisOnlineTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $this->set_form_actions($actions);
        $this->set_default_row_count(1000);
    }
}
?>