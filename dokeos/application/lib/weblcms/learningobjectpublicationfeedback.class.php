<?php
/**
 * @package application.weblcms
 */
/**
 * This class represents a learning object publication feedback object.
 */
class LearningObjectPublicationFeedback
{
   /**
    * Constant defining a property of the publication
 	*/
	const PROPERTY_ID = 'id';
	const PROPERTY_PUBLICATION_OBJECT_ID = 'publication';
	const PROPERTY_LEARNING_OBJECT_ID = 'learning_object';
	/**#@-*/
   
   /**#@+
    * Property of this publication. See {@link LearningObjectPublication} for
    * more information about this property.
 	*/
	private $id;
	private $publication; 
	private $learningObject;
	/**#@-*/
	
	/**
	 * Constructor
	 * @param int $id The id of this learning object publiction
	 * @param int $publication The id of the publication 
	 * @param int $learningObject_id The learning object id which is published by this publication
	 */
	function LearningObjectPublicationFeedback($id, $publication, $learningObject_id)
	{
		$this->id = $id;
		$this->publication = $publication;
		$dm = RepositoryDataManager :: get_instance();	
		$this->learningObject = $dm->retrieve_learning_object($learningObject_id);	
	}
	
   /**
    * Gets the publication feedback id.
    * @return int
 	*/
	function get_id()
	{
		return $this->id;
	}
   /**
    * Gets the publication id.
    * @return int
 	*/
	function get_publication()
	{
		return $this->publication;
	}
	/**
	 * Gets the learning object.
	 * @return LearningObject
	 */
	function get_learning_object()
	{
		return $this->learningObject;
	}
	
   /**
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
	
	function set_publication($publication)
	{
		$this->publication = $publication;
	}

	function create()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$id = $dm->get_next_learning_object_publication_feedback_id();
		$this->set_id($id);
		return $dm->create_learning_object_publication_feedback($this);
	}
	/**
	 * Updates this publication feedback in persistent storage
	 * @see WeblcmsDataManager::update_learning_object_publication()
	 */
	function update()
	{
		return WeblcmsDataManager :: get_instance()->update_learning_object_publication_feedback($this);
	}
	/**
	 * Deletes this publication feedback from persistent storage
	 * @see WeblcmsDataManager::delete_learning_object_publication()
	 */
	function delete()
	{
		return WeblcmsDataManager :: get_instance()->delete_learning_object_publication_feedback($this);
	}
}
?>