<?php
abstract class LearningObjectPublisherComponent
{
	private $owner;

	private $type;

	private $additionalParameters;

	function LearningObjectPublisherComponent($owner, $type)
	{
		$this->owner = $owner;
		$this->type = $type;
		$this->additionalParameters = array ();
	}

	protected function get_owner()
	{
		return $this->owner;
	}

	protected function get_type()
	{
		return $this->type;
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