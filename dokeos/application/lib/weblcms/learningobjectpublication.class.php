<?php
class LearningObjectPublication
{
	const PROPERTY_ID = 'id';
	const PROPERTY_LEARNING_OBJECT_ID = 'learning_object';
	const PROPERTY_COURSE_ID = 'course';
	const PROPERTY_TOOL = 'tool';
	const PROPERTY_CATEGORY_ID = 'category';
	const PROPERTY_FROM_DATE = 'from_date';
	const PROPERTY_TO_DATE = 'to_date';
	const PROPERTY_HIDDEN = 'hidden';
	const PROPERTY_PUBLISHER_ID = 'publisher';
	const PROPERTY_PUBLICATION_DATE = 'published';
	const PROPERTY_DISPLAY_ORDER_INDEX = 'display_order';

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

	private $publisher;

	private $publicationDate;

	private $displayOrder;

	function LearningObjectPublication($id, $learningObject, $course, $tool,$category, $targetUsers, $targetGroups, $fromDate, $toDate, $publisher, $publicationDate, $hidden, $displayOrder)
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
		$this->publisher = $publisher;
		$this->publicationDate = $publicationDate;
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

	function get_publisher_id()
	{
		return $this->publisher;
	}

	function get_publication_date()
	{
		return $this->publicationDate;
	}

	function is_hidden()
	{
		return $this->hidden;
	}

	function is_forever()
	{
		return $this->get_from_date() == 0 && $this->get_to_date() == 0;
	}

	function is_for_everybody()
	{
		return (!count($this->get_target_users()) && !count($this->get_target_groups()));
	}
	
	function is_visible_for_target_users()
	{
		return (!$this->is_hidden()) && ( $this->is_forever() || ($this->get_from_date() <= time() && time() <= $this->get_to_date()) );
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

	function set_publisher_id($publisher)
	{
		$this->publisher = $publisher;
	}

	function set_publication_date($publicationDate)
	{
		$this->publicationDate = $publicationDate;
	}

	function set_hidden($hidden)
	{
		$this->hidden = $hidden;
	}

	function set_display_order_index($displayOrder)
	{
		$this->displayOrder = $displayOrder;
	}

	function toggle_visibility()
	{
		$this->set_hidden(!$this->is_hidden());
	}
	
	function create()
	{
		$dm = WebLCMSDataManager :: get_instance();
		$id = $dm->get_next_learning_object_publication_id();
		$this->set_id($id);
		return $dm->create_learning_object_publication($this);
	}

	function update()
	{
		return WebLCMSDataManager :: get_instance()->update_learning_object_publication($this);
	}
	
	function delete()
	{
		return WebLCMSDataManager :: get_instance()->delete_learning_object_publication($this);
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