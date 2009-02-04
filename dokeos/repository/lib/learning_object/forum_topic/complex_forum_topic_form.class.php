<?php
/**
 * @package repository.learningobject
 * @subpackage forum_topic
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

class ComplexForumTopicForm extends ComplexLearningObjectItemForm
{
	const TOTAL_PROPERTIES = 3;
	// Inherited
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('None'),'',0);
    	$this->addElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('Sticky'),'',1);
    	$this->addElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('Important'),'',2);
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->addElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('None'),'',0);
    	$this->addElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('Sticky'),'',1);
    	$this->addElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('Important'),'',2);
	}
	// Inherited
	function setDefaults($defaults = array ())
	{
		$cloi = $this->get_complex_learning_object_item();
	
		if (isset ($cloi))
		{
			$defaults[ComplexForumTopic :: PROPERTY_TYPE] = $cloi->get_type();
		}
		parent :: setDefaults($defaults);
	}

	function set_csv_values($valuearray)
	{	
		$defaults[ComplexForumTopic :: PROPERTY_TYPE] = $valuearray[0];
		parent :: set_values($defaults);
	}

	// Inherited
	function create_complex_learning_object_item()
	{ 
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_type($values[ComplexForumTopic :: PROPERTY_TYPE]); 
		return parent :: create_complex_learning_object_item();
	}
	// Inherited
	function update_complex_learning_object_item()
	{
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_type($values[ComplexForumTopic :: PROPERTY_TYPE]);
		return parent :: update_complex_learning_object_item();
	}
}

?>