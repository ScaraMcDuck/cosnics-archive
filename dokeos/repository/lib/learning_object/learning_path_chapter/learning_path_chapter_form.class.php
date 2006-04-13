<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/learning_path_chapter.class.php';
class LearningPathChapterForm extends LearningObjectForm
{
	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form($default_learning_object);
		$this->addElement('text', LearningPathChapter :: PROPERTY_DISPLAY_ORDER, get_lang('DisplayOrder'));
		$this->add_submit_button();
	}
	public function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->setDefaults();
		$this->addElement('text', LearningPathChapter :: PROPERTY_DISPLAY_ORDER, get_lang('DisplayOrder'));
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults[LearningPathChapter :: PROPERTY_DISPLAY_ORDER] = $lo->get_display_order();
		}
		parent :: setDefaults($defaults);
	}
	function create_learning_object($owner)
	{
		$object = new LearningPathChapter();
		$object->set_display_order($this->exportValue(LearningPathChapter :: PROPERTY_DISPLAY_ORDER));
		$this->set_learning_object($object);
		return parent :: create_learning_object($owner);
	}
	function update_learning_object(& $object)
	{
		$object->set_display_order($this->exportValue(LearningPathChapter :: PROPERTY_DISPLAY_ORDER));
		return parent :: update_learning_object(& $object);
	}
}
?>