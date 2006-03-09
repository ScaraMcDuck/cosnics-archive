<?php
abstract class LearningObjectPublisherComponent
{
	private $owner;

	private $type;

	function LearningObjectPublisherComponent($owner, $type)
	{
		$this->owner = $owner;
		$this->type = $type;
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
}
?>