<?php
/**
 * @package repository.usertable
 */
require_once dirname(__FILE__) . '/event_table_column_model.class.php';
require_once dirname(__FILE__) . '/event_table_column.class.php';
require_once dirname(__FILE__) . '/../event.class.php';

/**
 * TODO: Add comment
 */
class DefaultEventTableColumnModel extends EventTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultEventTableColumnModel()
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
        $columns[] = new EventTableColumn(Event :: PROPERTY_BLOCK, true);
        $columns[] = new EventTableColumn(Event :: PROPERTY_NAME, true);
        return $columns;
    }
}
?>