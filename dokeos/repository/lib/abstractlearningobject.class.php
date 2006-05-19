<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/learningobject.class.php';

class AbstractLearningObject extends LearningObject
{
	private $type;
	
	private $attachments_supported;
	
	function AbstractLearningObject($type, $owner, $parent = 0)
	{
		parent :: __construct();
		$this->type = $type;
		$this->attachments_supported = false;
		$this->set_owner_id($owner);
		$this->set_parent_id($parent);
	}
	
	function get_type()
	{
		return $this->type;
	}
	
	function supports_attachments()
	{
		$class = LearningObject :: type_to_class($this->get_type());
		$dummy_object = new $class();
		return $dummy_object->supports_attachments();
	}
}
?>