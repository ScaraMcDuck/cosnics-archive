<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents a topic in a discussion forum.
 */
class ForumTopic extends LearningObject
{
	const PROPERTY_LOCKED = 'locked';
	const PROPERTY_EMAIL_NOTIFICATION = 'notify';
	const EMAIL_NOTIFICATION_SEP = ":";
	private $emails_first_access = true;

	function get_locked()
	{
		return $this->get_additional_property(self :: PROPERTY_LOCKED);
	}
	
	function set_locked($locked)
	{
		return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
	}
	
	static function get_additional_property_names()
	{
		/*return array (self :: PROPERTY_MAIL_NOTIFICATION, self :: PROPERTY_LOCKED);*/
		return array (self :: PROPERTY_LOCKED);
	}
	
	function get_allowed_types()
	{
		return array('forum_post');
	}
	

	/**
	 * Return an associative array containing the list of email addresses who need
	 * to be notified when new messages are posted in this topic.
	 */
	/*function get_notification_emails()
	{
		$list = $this->get_additional_property(self :: PROPERTY_EMAIL_NOTIFICATION);
		if ($this->emails_first_access)
		{
			$this->emails_first_access = false;
			$list = unserialize($list);
			$this->set_additional_property(self :: PROPERTY_EMAIL_NOTIFICATION, $list);
		}
		return $list; 
	}*/
	
	/**
	 * Add a new email to the list of notifications
	 * @param string email to be added to the list
	 */
	/*function add_notification_email($email)
	{
		$list = self :: get_notification_emails();
		$list[$email] = $email;
		$this->set_additional_property(self :: PROPERTY_EMAIL_NOTIFICATION, $list);
	}*/

	/**
	 * Remove all occurences of an email from the list of notifications
	 * @param string email to be removed from the list (not case-sensitive)
	 */
	/*function del_notification_email($email)
	{
		$list = self :: get_notification_emails();
		if (isset($list[$email]))
		{
			unset($list[$email]);
		}
		$this->set_additional_property(self :: PROPERTY_EMAIL_NOTIFICATION, $list);
	}*/
	
	/**
	 * Override the parent's update method because we need to serialize
	 * the email notification list.
	 * 
	 */
	/*function update()
	{
		if (!$this->emails_first_access)
		{
			$this->emails_first_access = true;
			$list = $this->get_additional_property(self :: PROPERTY_EMAIL_NOTIFICATION);
			$this->set_additional_property(self :: PROPERTY_EMAIL_NOTIFICATION, serialize($list));
			parent :: update();
		}
	}*/
	
	/**
	 * When creating a new forum topic, a first forum post in that topic will
	 * also be created. This post has the exact same properties as the topic
	 * (title, description, owner,...)
	 */
	/*function create()
	{
		parent::create();
		$post = new ForumPost();
		$post->set_title($this->get_title());
		$post->set_description($this->get_description());
		$post->set_parent_id($this->get_id());
		$post->set_owner_id($this->get_owner_id());
		$post->create();
	}*/
	
}
?>