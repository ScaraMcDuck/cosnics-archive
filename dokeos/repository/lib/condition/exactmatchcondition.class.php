<?php
require_once dirname(__FILE__).'/condition.class.php';

/**
==============================================================================
 *	This class represents a selection condition that requires an exact match.
 *	An example of an instance would be a condition that requires that the ID
 *	of a learning object be the number 4.
 * 
 *	@author Tim De Pauw
==============================================================================
 */

class ExactMatchCondition implements Condition
{
	private $name;

	private $value;

	function ExactMatchCondition($name, $value)
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
}
?>