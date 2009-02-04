<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumPost extends LearningObject
{
	// Stores whether the user should get notified if
	// someone replies to the topic.
	const PROPERTY_NOTIFICATION = 'reply_notification';
	const NOTIFY_NONE  = 1;	
	const NOTIFY_TOPIC = 2;

	function supports_attachments()
	{
		return true;
	}
	
	// Inherited
	function is_master_type()
	{
		return false;
	}
	
	function is_versionable()
	{
		return false;
	}
	
	/*function get_allowed_types()
	{
		return array('forum_post');
	}*/
}
?>