<?php
/**
 * $Id$
 * @package repository.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 *	This class represents a selection condition that requires an equality.
 *	An example of an instance would be a condition that requires that the ID
 *	of a learning object be the number 4.
 *
 *	@author Tim De Pauw
 */
class EqualityCondition implements Condition
{
    /**
     * Name
     */
    private $name;
    /**
     * Value
     */
    private $value;

    /**
     * Constructor
     * @param string $name
     * @param string $value
     */
    function EqualityCondition($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
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
     * Gets the value
     * @return string
     */
    function get_value()
    {
        return $this->value;
    }

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        return $this->get_name() . ' = \'' . $this->get_value() . '\'';
    }
}
?>