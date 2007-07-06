<?php
/**
 * $Id: orcondition.class.php 9192 2006-09-01 12:17:40Z bmol $
 * @package repository.condition
 */
require_once dirname(__FILE__).'/multipleaggregatecondition.class.php';
/**
 *	This type of condition requires that one or more of its aggregated
 *	conditions be met.
 *
 *	@author Tim De Pauw
 */
class OrCondition extends MultipleAggregateCondition
{
	/**
	 * Gets a string representation of this condition
	 * @return string
	 */
	function __toString()
	{
		$conditions = $this->get_conditions();
		foreach($conditions as $index => $condition)
		{
			$cond_string[] = '('.$condition->__toString().')';
		}
		return implode(' OR ',$cond_string);
	}
}
?>