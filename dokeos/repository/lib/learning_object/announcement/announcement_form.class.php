<?php
/**
 * @package repository.learningobject.announcement
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/announcement.class.php';
class AnnouncementForm extends LearningObjectForm
{
	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form($default_learning_object);
		$this->add_submit_button();
	}
	public function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->setDefaults();
		$this->add_submit_button();
	}
	function create_learning_object($owner)
	{
		$object = new Announcement();
		$this->set_learning_object($object);
		return parent :: create_learning_object($owner);
	}
}
?>