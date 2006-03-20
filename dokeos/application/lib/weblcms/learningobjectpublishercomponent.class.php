<?php
abstract class LearningObjectPublisherComponent
{
	private $parent;
	
	private $owner;

	private $types;
	
	function LearningObjectPublisherComponent($parent, $owner, $types)
	{
		$this->parent = $parent;
		$this->owner = $owner;
		$this->types = (is_array($types) ? $types : array($types));
	}
	
	protected function get_parent()
	{
		return $this->parent;
	}

	protected function get_owner()
	{
		return $this->owner;
	}

	protected function get_types()
	{
		return $this->types;
	}

	abstract function as_html();
	
	function get_url($parameters = array())
	{
		return $this->parent->get_url($parameters);
	}

	function get_parameters()
	{
		return $this->parent->get_parameters();
	}
	
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}
	
	function get_categories()
	{
		return $this->parent->get_categories();
	}
}
?>