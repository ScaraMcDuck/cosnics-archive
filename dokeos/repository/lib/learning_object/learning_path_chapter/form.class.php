<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
class LearningPathChapterForm extends LearningObjectForm
{
	public function LearningPathChapterForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form($default_learning_object);
		$this->addElement('text', 'display_order', 'Display order');
		$this->add_submit_button();
	}
	public function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->setDefaults();
		$this->addElement('text', 'display_order', 'Display order');
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['display_order'] = $lo->get_display_order();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = RepositoryDataManager::get_instance();
		$learningPathChapter = new LearningPathChapter();
		$learningPathChapter->set_owner_id($owner);
		$learningPathChapter->set_title($values['title']);
		$learningPathChapter->set_description($values['description']);
		$learningPathChapter->set_display_order($values['display_order']);
		$learningPathChapter->create();
		return $learningPathChapter;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_display_order($values['display_order']);
		$object->update();
	}
}
?>