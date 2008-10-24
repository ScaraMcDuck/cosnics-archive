<?php
/**
 * $Id$
 * @package repository
 */
require_once dirname(__FILE__).'/learning_object.class.php';
/**
 * An abstract learning object
 */
class AbstractLearningObject extends LearningObject
{
	/**
	 * The type of the learning object
	 */
	private $type;
	/**
	 * Are attachments supported
	 */
	private $attachments_supported;
	/**
	 * Constructor
	 * @param string $type
	 * @param int $owner
	 * @param int $parent
	 */
	function AbstractLearningObject($type, $owner, $parent = 0)
	{
		parent :: __construct();
		$this->type = $type;
		$this->attachments_supported = false;
		$this->set_owner_id($owner);
		$this->set_parent_id($parent);
	}
	/**
	 * Gets the type of this abstract learning object
	 * @return string
	 */
	function get_type()
	{
		return $this->type;
	}
	/**
	 * Determines if this object supports attachments
	 * @return boolean
	 */
	function supports_attachments()
	{
		$dummy_object = LearningObject :: factory($this->get_type());
		return $dummy_object->supports_attachments();
	}
	
	function is_versionable()
	{
		$dummy_object = LearningObject :: factory($this->get_type());
		return $dummy_object->is_versionable();
	}
}
?>