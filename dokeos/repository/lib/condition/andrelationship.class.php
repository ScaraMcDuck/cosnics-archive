<?php
require_once dirname(__FILE__).'/relationship.class.php';

class AndRelationship implements Relationship
{
	private $conditions;

	function AndRelationship($conditions)
	{
		$this->conditions = $conditions;
	}

	function get_conditions()
	{
		return $this->conditions;
	}
}
?>