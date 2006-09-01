<?php
/**
 * $Id$
 * @package repository.learningobjecttable
 */
/**
 * This interface defines how learning object tables access the learning
 * objects they display. You will need to implement it if you wish to create
 * a learning object table.
 *
 * @see LearningObjectTable
 * @author Tim De Pauw
 */
interface LearningObjectTableDataProvider
{
	/**
	 * Retrieves the learning objects to display in the table.
	 * @param int $offset The index of the first object to return.
	 * @param int $count The maximum number of objects to return.
	 * @param string $order_property The property to order the objects by. One
	 *                               of the LearningObject :: PROPERTY_*
	 *                               constants.
	 * @param string $order_direction The order direction. Either the PHP
	 *                                constant SORT_ASC or SORT_DESC.
	 * @return ResultSet A result set providing LearningObject instances.
	 * @see ResultSet
	 */
    function get_learning_objects($offset, $count, $order_property, $order_direction);

    /**
     * Retrieves the total number of learning objects that may be displayed.
     * @return int The number of objects.
     */
    function get_learning_object_count();
}
?>