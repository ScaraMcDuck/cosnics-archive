<?php
/**
 * $Id: announcement.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage system_announcement
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents a system announcement
 */
class SystemAnnouncement extends LearningObject
{
	//Inherited
	function supports_attachments()
	{
		return false;
	}
}
?>