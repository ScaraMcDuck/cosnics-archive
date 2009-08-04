<?php
/**
 * @package repository.usertable
 */

require_once dirname(__FILE__) . '/../event.class.php';
/**
 * TODO: Add comment
 */
class DefaultEventTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultEventTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param LearningObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $learning_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $event)
    {
        if ($property = $column->get_property())
        {
            switch ($property)
            {
                case Event :: PROPERTY_NAME :
                    return $event->get_name();
                case Event :: PROPERTY_BLOCK :
                    return $event->get_block();
            }
        }
        return '&nbsp;';
    }
    
	function render_id_cell($event)
    {
    	return $event->get_id();
    }
}
?>