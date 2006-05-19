<?php
/**
 * @package repository.condition
 */
require_once dirname(__FILE__).'/aggregatecondition.class.php';

/**
==============================================================================
 *	This type of aggregate condition negates a single condition, thus
 *	requiring that that condition not be met.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class NotCondition extends AggregateCondition
{
	private $condition;

	function NotCondition($condition)
	{
		$this->condition = $condition;
	}

	function get_condition()
	{
		return $this->condition;
	}
	function __toString()
	{
		return ' NOT ('.$this->get_condition().')';
	}
}
?>