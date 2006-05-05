<?php
class LearningObjectTableColumn
{
	private $learning_object_property;

	private $title;

	private $sortable;

	function LearningObjectTableColumn($property_name_or_column_title, $contains_learning_object_property = false)
	{
		if ($contains_learning_object_property)
		{
			$this->learning_object_property = $property_name_or_column_title;
			// TODO: Restore this when localized strings are available.
			//$this->title = get_lang($this->learning_object_property.'LearningObjectPropertyTitle');
			$this->title = ucfirst($this->learning_object_property);
			$this->sortable = true;
		}
		else
		{
			$this->title = $property_name_or_column_title;
			$this->sortable = false;
		}
	}
	
	function get_learning_object_property()
	{
		return $this->learning_object_property;
	}
	
	function get_title()
	{
		return $this->title;
	}
	
	function is_sortable()
	{
		return $this->sortable;
	}
	
	function set_title($title)
	{
		$this->title = $title;
	}
	
	function set_sortable($sortable)
	{
		$this->sortable = $sortable;
	}
}
?>