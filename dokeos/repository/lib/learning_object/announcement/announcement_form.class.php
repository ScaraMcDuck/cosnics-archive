<?php
/**
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/announcement.class.php';
/**
 * This class represents a form to create or update announcements
 */
class AnnouncementForm extends LearningObjectForm
{
	// Inherited
	function create_learning_object()
	{
		$object = new Announcement();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>