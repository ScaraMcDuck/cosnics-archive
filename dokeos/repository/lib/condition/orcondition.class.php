<?php
require_once dirname(__FILE__).'/aggregatecondition.class.php';

/**
==============================================================================
 *	This type of condition requires that one or more of its aggregated
 *	conditions be met. The aggregated conditions must be passed to the
 *	constructor as an array.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class OrCondition extends AggregateCondition
{
	private $conditions;

	function OrCondition($conditions)
	{
		$this->conditions = $conditions;
	}

	function get_conditions()
	{
		return $this->conditions;
	}
}
?>