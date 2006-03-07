<?php
require_once dirname(__FILE__).'/aggregatecondition.class.php';

/**
==============================================================================
 *	This class represents a condition that consists of multiple aggregated
 *	conditions. Thus, it is used to model a single relationship (AND, OR
 *	and perhaps others) between its aggregated conditions.
 * 
 *	@author Tim De Pauw
==============================================================================
 */

abstract class MultipleAggregateCondition extends AggregateCondition
{
	private $conditions;

	/**
	 * Constructor.
	 * @param mixed $conditions The aggregated conditions, as either a list
	 *                          or an array of Condition objects.
	 */
	function MultipleAggregateCondition($conditions)
	{
		$this->conditions = (func_num_args() == 1 ? $conditions : func_get_args());
	}

	function get_conditions()
	{
		return $this->conditions;
	}
}
?>