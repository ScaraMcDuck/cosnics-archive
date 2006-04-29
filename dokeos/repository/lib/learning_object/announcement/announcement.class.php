<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage announcement
 */
class Announcement extends LearningObject
{
	function supports_attachments()
	{
		return true;
	}
}
?>