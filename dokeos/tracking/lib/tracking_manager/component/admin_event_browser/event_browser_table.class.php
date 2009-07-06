<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__) . '/../../../event_table/event_table.class.php';
require_once dirname(__FILE__) . '/event_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/event_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/event_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../tracking_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class EventBrowserTable extends EventTable
{

    /**
     * Constructor
     * @see LearningObjectTable::LearningObjectTable()
     */
    function EventBrowserTable($browser, $name, $parameters, $condition)
    {
        $model = new EventBrowserTableColumnModel();
        $renderer = new EventBrowserTableCellRenderer($browser);
        $data_provider = new EventBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, $name, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $actions['enable'] = Translation :: get('Enable_selected_events');
        $actions['disable'] = Translation :: get('Disable_selected_events');
        $actions[TrackingManager :: ACTION_EMPTY_TRACKER] = Translation :: get('Empty_selected_events');
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>