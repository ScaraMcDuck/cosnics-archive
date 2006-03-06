<?php
require_once dirname(__FILE__).'/relationship.class.php';

class NotRelationship implements Relationship
{
	private $condition;

	function NotRelationship($condition)
	{
		$this->condition = $condition;
	}
	
	function get_condition()
	{
		return $this->condition;
	}
}
?>