<?php

require_once dirname(__FILE__) . '/objective.class.php';

class Objectives
{
	private $objectives;
	private $primary_objective;
	
	function Objectives()
	{
		$this->objectives = array();
	}
	
	function get_objectives()
	{
		return $this->objectives;
	}
	
	function set_objectives($objectives)
	{
		$this->objectives = $objectives;
	}
	
	function get_primary_objective()
	{
		return $this->primary_objective;
	}
	
	function set_primary_objective($primary_objective)
	{
		$this->primary_objective = $primary_objective;
	}
	
	function add_objective($objective, $primary = false)
	{
		if($primary)
			$this->primary_objective = $objective;
		else
			$this->objectives[] = $objective;
	}
	
	function get_objective($index)
	{
		return $this->objectives[$index];
	}
	
	function remove_objective($index)
	{
		$this->objectives[$index] = null;
		unset($this->objectives[$index]);
	}
}
?>