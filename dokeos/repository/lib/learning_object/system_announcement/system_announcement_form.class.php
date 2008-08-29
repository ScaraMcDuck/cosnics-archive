<?php
/**
 * $Id: announcement_form.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage system_announcement
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/system_announcement.class.php';
/**
 * This class represents a form to create or update system announcements
 */
class SystemAnnouncementForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('select', SystemAnnouncement :: PROPERTY_ICON, Translation :: get('icon'), SystemAnnouncement :: get_possible_icons());
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('select', SystemAnnouncement :: PROPERTY_ICON, Translation :: get('icon'), SystemAnnouncement :: get_possible_icons());
	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[SystemAnnouncement :: PROPERTY_ICON] = $lo->get_icon();
		}
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$object = new SystemAnnouncement();
		$object->set_icon($this->exportValue(SystemAnnouncement :: PROPERTY_ICON));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_icon($this->exportValue(SystemAnnouncement :: PROPERTY_ICON));
		return parent :: update_learning_object();
	}
}
?>
