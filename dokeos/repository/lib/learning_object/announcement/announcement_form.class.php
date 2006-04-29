<?php
/**
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/announcement.class.php';
class AnnouncementForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_footer();
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->setDefaults();
		$this->add_footer();
	}
	function create_learning_object()
	{
		$object = new Announcement();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>