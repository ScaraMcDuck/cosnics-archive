<?php
/**
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/announcement.class.php';
class AnnouncementForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new Announcement();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>