<?php
/**
 * $Id$
 * @package repository.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 * This class represents a selection condition that requires a value to be
 * present in a list of values.
 * An example of an instance would be a condition that requires that the ID of a
 * learning object be in the list {4,10,12}.
 *
 *	@author Bart Mollet
 */
class InCondition implements Condition
{
    /**
     * Name
     */
    private $name;
    /**
     * Values
     */
    private $values;

    /**
     * Constructor
     * @param string $name
     * @param array $values
     */
    function InCondition($name, $values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    /**
     * Gets the name
     * @return string
     */
    function get_name()
    {
        return $this->name;
    }

    /**
     * Gets the values
     * @return array
     */
    function get_values()
    {
        return $this->values;
    }

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        return $this->get_name() . ' IN {' . implode(',', $this->get_values()) . '}';
    }
}
?>