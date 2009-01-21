<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item_form.class.php';
require_once dirname(__FILE__) . '/complex_hotspot_question.class.php';
/**
 * This class represents a complex question
 */
class ComplexHotspotQuestionForm extends ComplexLearningObjectItemForm
{
 	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('text', ComplexHotspotQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'));
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->addElement('text', ComplexHotspotQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'));
	}
	// Inherited
	function setDefaults($defaults = array ())
	{
		$cloi = $this->get_complex_learning_object_item();
	
		if (isset ($cloi))
		{
			$defaults[ComplexHotspotQuestion :: PROPERTY_WEIGHT] = $cloi->get_weight();
		}
		parent :: setDefaults($defaults);
	}

	// Inherited
	function create_complex_learning_object_item()
	{ 
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_weight($values[ComplexHotspotQuestion :: PROPERTY_WEIGHT]); 
		return parent :: create_complex_learning_object_item();
	}
	// Inherited
	function update_complex_learning_object_item()
	{
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_weight($values[ComplexHotspotQuestion :: PROPERTY_WEIGHT]);
		return parent :: update_complex_learning_object_item();
	}
}
?>