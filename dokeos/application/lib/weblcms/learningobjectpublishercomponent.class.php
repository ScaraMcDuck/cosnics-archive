<?php
abstract class LearningObjectPublisherComponent
{
	private $owner;

	private $types;

	private $additionalParameters;

	function LearningObjectPublisherComponent($owner, $types)
	{
		$this->owner = $owner;
		$this->types = (is_array($types) ? $types : array($types));
		$this->additionalParameters = array ();
	}

	protected function get_owner()
	{
		return $this->owner;
	}

	protected function get_types()
	{
		return $this->types;
	}

	abstract function display();

	function get_additional_parameters()
	{
		return $this->additionalParameters;
	}

	function get_additional_parameter($name)
	{
		return $this->additionalParameters[$name];
	}

	function set_additional_parameter($name, $value)
	{
		$this->additionalParameters[$name] = $value;
	}
}
?>