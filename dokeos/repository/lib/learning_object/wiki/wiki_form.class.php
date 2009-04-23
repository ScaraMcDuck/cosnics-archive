<?php
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/wiki.class.php';
/**
 * @package repository.learningobject
 * @subpackage wiki
 */
class WikiForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Wiki ::PROPERTY_LOCKED] = $valuearray[3];
		parent :: set_values($defaults);
	}
	function create_learning_object()
	{
		$object = new Wiki();
        $object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

    function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
		$this->set_learning_object($object);
		return parent :: update_learning_object();
	}

    function build_creation_form()
	{
		parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('WikiLocked'));
        $this->addElement('category');
	}

	function build_editing_form()
	{
		parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('Locked'));
        $this->addElement('category');
	}

}
?>
