<?php
require_once dirname(__FILE__).'/condition.class.php';

/**
==============================================================================
 *	@author Tim De Pauw
 * @package repository.condition
==============================================================================
 */

class InequalityCondition implements Condition
{
	const LESS_THAN = 1;

	const LESS_THAN_OR_EQUAL = 2;

	const GREATER_THAN = 3;

	const GREATER_THAN_OR_EQUAL = 4;

	private $name;

	private $operator;

	private $value;

	function InequalityCondition($name, $operator, $value)
	{
		$this->name = $name;
		$this->operator = $operator;
		$this->value = $value;
	}

	function get_name()
	{
		return $this->name;
	}

	function get_operator()
	{
		return $this->operator;
	}

	function get_value()
	{
		return $this->value;
	}
}
?>