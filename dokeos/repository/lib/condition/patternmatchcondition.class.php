<?php
require_once dirname(__FILE__) . '/condition.class.php';

/**
==============================================================================
 *	This class represents a selection condition that uses a pattern for
 *	matching. An example of an instance would be a condition that requires
 *	that the title of a learning object containts the word "math". The pattern
 *	is case insensitive and supports two types of wildcard characters: an
 *	asterisk (*) must match any sequence of characters, and a question mark
 *	(?) must match a single character.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class PatternMatchCondition implements Condition
{
	private $name;

	private $pattern;

	function PatternMatchCondition($name, $pattern) {
		$this->name = $name;
		$this->pattern = $pattern;
	}

	function get_name () {
		return $this->name;
	}

	function get_pattern () {
		return $this->pattern;
	}
	function __toString()
	{
		$result = $this->name.' = \''.$this->pattern.'\'';
		return $result;
	}
}
?>