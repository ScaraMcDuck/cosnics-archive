<?php
/**
 * $Id: in_condition.class.php 15426 2008-05-26 19:37:50Z Scara84 $
 * @package repository.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 * This class represents a subselect condition
 *
 *	@author Sven Vanpoucke
 */
class SubselectCondition implements Condition
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
     * Table
     */
    private $storage_unit;

    /**
     * Condition
     */
    private $condition;

    /**
     * Constructor
     * @param string $name
     * @param array $values
     */
    function SubselectCondition($name, $value, $storage_unit, $condition)
    {
        $this->name = $name;
        $this->value = $value;
        $this->storage_unit = $storage_unit;
        $this->condition = $condition;
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
     * Gets the storage_unit name for this subselect condition
     * @return string
     */
    function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     * Gets the condition for the subselected storage_unit
     */
    function get_condition()
    {
        return $this->condition;
    }

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        if ($this->get_condition())
        {
            $where = ' WHERE ' . $this->get_condition();
        }

        return $this->get_name() . ' IN (SELECT ' . $this->get_value() . ' FROM ' . $this->get_storage_unit() . $where . ')';
    }
}
?>