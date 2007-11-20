<?php
/**
 * $Id$
 * @package application.weblcms
 */
/**
 * This class represents a learning object publication.
 *
 * When publishing a learning object from the repository in the weblcms
 * application, a new object of this type is created.
 */
class LearningObjectPublication
{
   /**#@+
    * Constant defining a property of the publication
 	*/
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
	const PROPERTY_EMAIL_SENT = 'email_sent';
	/**#@-*/
   /**#@+
    * Property of this publication. See {@link LearningObjectPublication} for
    * more information about this property.
 	*/
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
	private $emailSent;
	/**#@-*/
	/**
	 * Constructor
	 * @param int $id The id of this learning object publiction
	 * @param LearningObject $learningObject The learning object which is
	 * published by this publication
	 * @param string $course The course code of the course where this
	 * publication is made
	 * @param string $tool The tool where this publication is made
	 * @param int $category The id of the learning object publication category
	 * in which this publication is stored
	 * @param array $targetUsers The users for which this publication is made.
	 * If this array contains no elements, the publication is for everybody.
	 * @param array $targetGroups The groups for which this publication is made.
	 * If this array contains no elements, the publication is for everybody.
	 * @param int $fromDate The date on which this publication should become
	 * available. If value is 0, publication is available forever.
	 * @param int $toDate The date on which this publication should become
	 * unavailable. If value is 0, publication is available forever.
	 * @param int $publisher The user id of the person who created this
	 * publication.
	 * @param int $publicationDate The date on which this publication was made.
	 * @param boolean $hidden If true, this publication is invisible
	 * @param int $displayOrder The display order of this publication in its
	 * location (course - tool - category)
	 */
	function LearningObjectPublication($id, $learningObject, $course, $tool,$category, $targetUsers, $targetGroups, $fromDate, $toDate, $publisher, $publicationDate, $hidden, $displayOrder, $emailSent)
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
		$this->emailSent = $emailSent;
	}
   /**
    * Gets the publication id.
    * @return int
 	*/
	function get_id()
	{
		return $this->id;
	}
	/**
	 * Gets the learning object.
	 * @return LearningObject
	 */
	function get_learning_object ()
	{
		return $this->learningObject;
	}
	/**
	 * Gets the course code of the course in which this publication was made.
	 * @return string The course code
	 */
	function get_course_id()
	{
		return $this->course;
	}
	/**
	 * Gets the tool in which this publication was made.
	 * @return string
	 */
	function get_tool()
	{
		return $this->tool;
	}
	/**
	 * Gets the id of the learning object publication category in which this
	 * publication was made
	 * @return int
	 */
	function get_category_id()
	{
		return $this->category;
	}
	/**
	 * Gets the list of target users of this publication
	 * @return array An array of user ids.
	 * @see is_for_everybody()
	 */
	function get_target_users()
	{
		return $this->targetUsers;
	}
	/**
	 * Gets the list of target groups of this publication
	 * @return array An array of group ids.
	 * @see is_for_everybody()
	 */
	function get_target_groups()
	{
		return $this->targetGroups;
	}
	/**
	 * Gets the date on which this publication becomes available
	 * @return int
	 * @see is_forever()
	 */
	function get_from_date()
	{
		return $this->fromDate;
	}
	/**
	 * Gets the date on which this publication becomes unavailable
	 * @return int
	 * @see is_forever()
	 */
	function get_to_date()
	{
		return $this->toDate;
	}
	/**
	 * Gets the user id of the user who made this publication
	 * @return int
	 */
	function get_publisher_id()
	{
		return $this->publisher;
	}
	/**
	 * Gets the date on which this publication was made
	 * @return int
	 */
	function get_publication_date()
	{
		return $this->publicationDate;
	}
	/**
	 * Determines whether this publication was sent by email to the users and
	 * groups for which this publication was made
	 * @return boolean True if an email was sent
	 */
	function is_email_sent()
	{
		return $this->emailSent;
	}
	/**
	 * Determines whether this publication is hidden or not
	 * @return boolean True if the publication is hidden.
	 */
	function is_hidden()
	{
		return $this->hidden;
	}
	/**
	 * Determines whether this publication is available forever
	 * @return boolean True if the publication is available forever
	 * @see get_from_date()
	 * @see get_to_date()
	 */
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


   /**#@+
    * Sets a property of this learning object publication.
    * See constructor for detailed information about the property.
    * @see LearningObjectPublication()
 	*/
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
	function set_email_sent($emailSent)
	{
		$this->emailSent = $emailSent;
	}
	/**#@-*/
	/**
	 * Toggles the visibility of this publication.
	 */
	function toggle_visibility()
	{
		$this->set_hidden(!$this->is_hidden());
	}
	/**
	 * Creates this publication in persistent storage
	 * @see WeblcmsDataManager::create_learning_object_publication()
	 */
	function create()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$id = $dm->get_next_learning_object_publication_id();
		$this->set_id($id);
		return $dm->create_learning_object_publication($this);
	}
	/**
	 * Updates this publication in persistent storage
	 * @see WeblcmsDataManager::update_learning_object_publication()
	 */
	function update()
	{
		return WeblcmsDataManager :: get_instance()->update_learning_object_publication($this);
	}
	/**
	 * Deletes this publication from persistent storage
	 * @see WeblcmsDataManager::delete_learning_object_publication()
	 */
	function delete()
	{
		return WeblcmsDataManager :: get_instance()->delete_learning_object_publication($this);
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
		return WeblcmsDataManager :: get_instance()->move_learning_object_publication($this, $places);
	}
	
	function get_feedback()
	{
		return WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_feedback($this);
	}
}
?>