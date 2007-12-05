<?php
/**
 * @package application.weblcms
 */

/**
 * This class represents a learning object publication feedback.
 *
 * When publishing a learning object from the repository in the weblcms
 * application, attached to another learning object, a new object of this type is created.
 */
class LearningObjectPublicationFeedback extends LearningObjectPublication
{
    /**
	 * Constructor
	 * @param int $id The id of this learning object publiction
	 * @param LearningObject $learningObject The learning object which is
	 * published by this publication
	 * @param string $course The course code of the course where this
	 * publication is made
	 * @param string $tool The tool where this publication is made
	 * @param int $parent_id The id of this learning object publication parent
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
	 * @param int $modifiedDate The date on which this publication was updated.
	 * @param boolean $hidden If true, this publication is invisible
	 * @param int $displayOrder The display order of this publication in its
	 * location (course - tool - category)
	 */
	function LearningObjectPublicationFeedback($id, $learningObject, $course, $tool, $parent_id,$publisher, $publicationDate, $modifiedDate, $hidden, $emailSent)
	{
		
		parent :: LearningObjectPublication($id, $learningObject, $course, $tool, 0, array(), array(), 0, 0, $publisher, $publicationDate, $modifiedDate, $hidden, 0, $emailSent);
		$this->set_parent_id($parent_id);
	}
   
   /*
    * Sets a property of this learning object publication.
    * See constructor for detailed information about the property.
    * @see LearningObjectPublicationFeedback()
 	*/
	
	function set_category_id($category)
	{
		parent :: set_category(0);
	}

	function set_target_users($targetUsers)
	{
		parent :: set_target_users(array());
	}

	function set_target_groups($targetGroups)
	{
		parent :: set_target_groups(array());
	}

	function set_from_date($fromDate)
	{
		parent :: set_from_date(0);
	}

	function set_to_date($toDate)
	{
		parent :: set_to_date(0);
	}

	function set_hidden($hidden)
	{
		parent :: set_hidden(0);
	}

	function set_display_order_index($displayOrder)
	{
		parent :: set_display_order_index(0);
	}
	function set_email_sent($emailSent)
	{
		parent :: set_email_sent(0);
	}
	function create() 
	{
		$dm = WeblcmsDataManager :: get_instance();
		$parent_object = $dm->retrieve_learning_object_publication($this->parent_id);
		$parent_object->set_modified_date(time());
		$parent_object->update();
		$id = $dm->get_next_learning_object_publication_id();
		$this->set_id($id);
		return $dm->create_learning_object_publication($this);
	}
	
	function update()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$parent_object = $dm->retrieve_learning_object_publication($this->parent_id);
		$parent_object->set_modified_date(time());
		$parent_object->update();
		return $dm->update_learning_object_publication($this);
	}
}
?>