<?php

class ConditionRule
{
	private $conditions;
	private $action;

	function ConditionRule()
	{
		$this->conditions = array();
		$this->action = null;	
	}

	function get_action()
	{
		return $this->action;
	}

	function get_conditions()
	{
		return $this->conditions;
	}

	function set_action($action)
	{
		$this->action = $action;
	}

	function set_conditions($conditions)
	{
		$this->conditions = $conditions;
	}
	
	function add_condition($condition)
	{
		$this->conditions[] = $condition;
	}
	
	function get_condition($index)
	{
		return $this->conditions[$index];
	}
	
}
?>