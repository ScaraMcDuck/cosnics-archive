<?php
require_once dirname(__FILE__).'/multipleaggregatecondition.class.php';

/**
==============================================================================
 *	This type of condition requires that all of its aggregated conditions be
 *	met.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class AndCondition extends MultipleAggregateCondition
{
	function __toString()
	{
		$conditions = $this->get_conditions();
		foreach($conditions as $index => $condition)
		{
			$cond_string[] = '('.$condition->__toString().')';
		}
		return implode(' AND ',$cond_string);
	}
}
?>