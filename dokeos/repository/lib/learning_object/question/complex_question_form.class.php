<?php
/**
 * @package repository.learningobject
 * @subpackage question
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__) . '/complex_question.class.php';

class ComplexQuestionForm extends ComplexLearningObjectItemForm
{
	const TOTAL_PROPERTIES = 1;
	// Inherited
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('text', ComplexQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'), array("size" => "50"));
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->addElement('text', ComplexQuestion :: PROPERTY_WEIGHT, Translation :: get('Score'), array("size" => "50"));
	}
	// Inherited
	function setDefaults($defaults = array ())
	{
		$cloi = $this->get_complex_learning_object_item();
	
		if (isset ($cloi))
		{
			$defaults[ComplexQuestion :: PROPERTY_WEIGHT] = $cloi->get_weight();
		}
		parent :: setDefaults($defaults);
	}

	function set_csv_values($valuearray)
	{	
		$defaults[ComplexQuestion :: PROPERTY_WEIGHT] = $valuearray[0];
		parent :: set_values($defaults);
	}

	// Inherited
	function create_complex_learning_object_item()
	{ 
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_weight($values[ComplexQuestion :: PROPERTY_WEIGHT]); 
		return parent :: create_complex_learning_object_item();
	}
	// Inherited
	function update_complex_learning_object_item()
	{
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_weight($values[ComplexQuestion :: PROPERTY_WEIGHT]);
		return parent :: update_complex_learning_object_item();
	}
}

?>