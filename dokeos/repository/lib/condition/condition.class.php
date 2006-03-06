<?php
abstract class Condition
{
	private $name;

	private $value;

	function Condition($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}
}
?>