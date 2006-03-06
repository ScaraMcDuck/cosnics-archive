<?php
require_once dirname(__FILE__).'/relationship.class.php';

class OrRelationship implements Relationship
{
	private $conditions;

	function OrRelationship($conditions)
	{
		$this->conditions = $conditions;
	}

	function get_conditions()
	{
		return $this->conditions;
	}
}
?>