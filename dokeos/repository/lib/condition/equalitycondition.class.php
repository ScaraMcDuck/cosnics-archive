<?php
require_once dirname(__FILE__).'/condition.class.php';

/**
==============================================================================
 *	This class represents a selection condition that requires an equality.
 *	An example of an instance would be a condition that requires that the ID
 *	of a learning object be the number 4.
 *
 *	@author Tim De Pauw
 * @package repository.condition
==============================================================================
 */

class EqualityCondition implements Condition
{
	private $name;

	private $value;

	function EqualityCondition($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}

	function get_name()
	{
		return $this->name;
	}

	function get_value()
	{
		return $this->value;
	}
	function __toString()
	{
		return $this->get_name().' = \''.$this->get_value().'\'';
	}
}
?>