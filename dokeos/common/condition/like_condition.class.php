<?php
/**
 * $Id: likecondition.class.php 13149 2007-09-21 07:03:23Z bmol $
 * @package repository.condition
 */
require_once dirname(__FILE__).'/condition.class.php';
/**
 *	This class represents a selection condition that requires a likeness.
 *	An example of an instance would be a condition that requires that the ID
 *	of a Object be the like the number 4 e.g. 44, 412, 514, etc.
 *
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
class LikeCondition implements Condition
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
	function LikeCondition($name, $value)
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
		return $this->get_name().' LIKE \'%'.$this->get_value().'%\'';
	}
}
?>