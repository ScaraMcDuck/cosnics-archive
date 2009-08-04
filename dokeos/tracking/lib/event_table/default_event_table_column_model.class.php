<?php
/**
 * @package repository.usertable
 */
require_once dirname(__FILE__) . '/../event.class.php';

/**
 * TODO: Add comment
 */
class DefaultEventTableColumnModel extends ObjectTableColumnModel 
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
        $columns[] = new ObjectTableColumn(Event :: PROPERTY_BLOCK, true);
        $columns[] = new ObjectTableColumn(Event :: PROPERTY_NAME, true);
        return $columns;
    }
}
?>