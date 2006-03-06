<?php
require_once dirname(__FILE__) . '/condition.class.php';

class ExactMatchCondition extends Condition
{
	private $name;
	
	private $value;
	
	function ExactMatchCondition($name, $value) {
		$this->name = $name;
		$this->value = $value;
	}
	
	function get_name () {
		return $this->name;
	}
	
	function get_value () {
		return $this->value;
	}
}
?>