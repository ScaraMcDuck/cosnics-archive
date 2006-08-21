<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumPost extends LearningObject
{
	const PROPERTY_PARENT_POST = 'parent_post';
	function supports_attachments()
	{
		return true;
	}
	/**
	 * Gets the parent post id
	 * @return int The parent post id
	 */
	function get_parent_post_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_PARENT_POST);
	}
	/**
	 * Sets the parent post id
	 * @param int $parent_post_id
	 */
	function set_parent_post_id ($parent_post_id)
	{
		return $this->set_additional_property(self :: PROPERTY_PARENT_POST, $parent_post_id);
	}
}
?>