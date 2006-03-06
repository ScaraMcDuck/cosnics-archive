<?php
require_once dirname(__FILE__) . '/condition.class.php';

class PatternMatchCondition extends Condition
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
}
?>