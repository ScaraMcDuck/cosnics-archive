<?php
/**
 * $Id: in_condition.class.php 15426 2008-05-26 19:37:50Z Scara84 $
 * @package repository.condition
 */
require_once dirname(__FILE__).'/condition.class.php';
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
	private $table;
	
	/**
	 * Condition
	 */
	private $condition;
	
	/**
	 * Constructor
	 * @param string $name
	 * @param array $values
	 */
	function SubselectCondition($name, $value, $table, $condition)
	{
		$this->name = $name;
		$this->value = $value;
		$this->table = $table;
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
	 * Gets the table name for this subselect condition
	 * @return string
	 */
	function get_table()
	{
		return $this->table;
	}
	
	/**
	 * Gets the condition for the subselected table
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
		if($this->get_condition())
		{
			$where = ' WHERE ' . $this->get_condition();	
		}
		
		return $this->get_name() . ' IN (SELECT ' . $this->get_value() . ' FROM ' . $this->get_table() . $where . ')';
	}
}
?>