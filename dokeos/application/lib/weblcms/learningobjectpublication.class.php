<?php
class LearningObjectPublication
{
	private $id;

	private $learningObject;

	private $course;

	private $tool;

	private $category;

	private $targetUsers;

	private $targetGroups;

	private $fromDate;

	private $toDate;

	private $hidden;

	private $displayOrder;

	function LearningObjectPublication($id, $learningObject, $course, $tool,$category, $targetUsers, $targetGroups, $fromDate, $toDate, $hidden, $displayOrder)
	{
		$this->id = $id;
		$this->learningObject = $learningObject;
		$this->course = $course;
		$this->tool = $tool;
		$this->category = $category;
		$this->targetUsers = $targetUsers;
		$this->targetGroups = $targetGroups;
		$this->fromDate = $fromDate;
		$this->toDate = $toDate;
		$this->hidden = $hidden;
		$this->displayOrder = $displayOrder;
	}

	function get_id()
	{
		return $this->id;
	}

	function get_learning_object ()
	{
		return $this->learningObject;
	}

	function get_course_id()
	{
		return $this->course;
	}

	function get_tool()
	{
		return $this->tool;
	}

	function get_category_id()
	{
		return $this->category;
	}

	function get_target_users()
	{
		return $this->targetUsers;
	}

	function get_target_groups()
	{
		return $this->targetGroups;
	}

	function get_from_date()
	{
		return $this->fromDate;
	}

	function get_to_date()
	{
		return $this->toDate;
	}

	function is_hidden()
	{
		return $this->hidden;
	}

	function get_display_order_index()
	{
		return $this->displayOrder;
	}

	function set_id($id)
	{
		$this->id = $id;
	}

	function set_learning_object($learningObject)
	{
		$this->learningObject = $learningObject;
	}

	function set_course_id($course)
	{
		$this->course = $course;
	}

	function set_category_id($category)
	{
		$this->category = $category;
	}

	function set_target_users($targetUsers)
	{
		$this->targetUsers = $targetUsers;
	}

	function set_target_groups($targetGroups)
	{
		$this->targetGroups = $targetGroups;
	}

	function set_from_date($fromDate)
	{
		$this->fromDate = $fromDate;
	}

	function set_to_date($toDate)
	{
		$this->toDate = $toDate;
	}

	function set_hidden($hidden)
	{
		$this->hidden = $hidden;
	}

	function set_display_order_index($displayOrder)
	{
		$this->displayOrder = $displayOrder;
	}

	function change_visibility()
	{
		$this->set_hidden(!$this->is_hidden());
	}

	function delete()
	{
		return WebLCMSDataManager :: get_instance()->delete_learning_object_publication($this);
	}

	function update()
	{
		return WebLCMSDataManager :: get_instance()->update_learning_object_publication($this);
	}

	/**
	 * Moves the publication up or down in the list.
	 * @param $places The number of places to move the publication down. A
	 *                negative number moves it up.
	 * @return int The number of places that the publication was moved
	 *             down. 
	 */
	function move($places)
	{
		return WebLCMSDataManager :: get_instance()->move_learning_object_publication($this, $places);
	}
}
?>