<?php
/**
 * @package learningobject.announcement
 */
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class StudentPublicationForm extends LearningObjectForm
{
	public function StudentPublicationForm($formName, $method = 'post', $action = null)
	{
		parent :: LearningObjectForm($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
		$this->addElement('text', 'url', 'Url');
		$this->addSubmitButton();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->addElement('text', 'url', 'Url');
		$this->setDefaults();
		$this->addSubmitButton();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['url'] = $lo->get_url();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = DataManager::get_instance();
		$studentPublication = new StudentPublication();
		$studentPublication->set_owner_id($owner);
		$studentPublication->set_title($values['title']);
		$studentPublication->set_description($values['description']);
		$studentPublication->set_url($values['url']);
		$studentPublication->create();
		return $studentPublication;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_url($values['url']);
		$object->update();
	}
}
?>