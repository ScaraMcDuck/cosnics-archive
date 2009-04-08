<?php
/**
 * @package repository.learningobject
 * @subpackage answer
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item_form.class.php';
require_once dirname(__FILE__) . '/complex_wiki_page.class.php';

class ComplexWikiPageForm extends ComplexLearningObjectItemForm
{
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$elements = $this->get_elements();
    	foreach($elements as $element)
    	{
    		$this->addElement($element);
    	}
    }

    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$elements = $this->get_elements();
    	foreach($elements as $element)
    	{
    		$this->addElement($element);
    	}
	}

    public function get_elements()
	{
		$elements[] = $this->createElement('checkbox', ComplexWikiPage :: PROPERTY_IS_HOMEPAGE, Translation :: get('Is homepage'));
		return $elements;
	}

    function setDefaults($defaults = array ())
	{
		$defaults = array_merge($defaults, $this->get_default_values());
		parent :: setDefaults($defaults);
	}

	function get_default_values()
	{
		$cloi = $this->get_complex_learning_object_item();

		if (isset ($cloi))
		{
			$defaults[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE] = $cloi->get_is_homepage() ? $cloi->get_is_homepage() : false;
		}

		return $defaults;
	}

    function create_complex_learning_object_item()
	{
		$values = $this->exportValues();
		$this->create_cloi_from_values($values);
	}

	function create_cloi_from_values($values)
	{
		$cloi = $this->get_complex_learning_object_item();
		$cloi->set_is_homepage($values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]);
		return parent :: create_complex_learning_object_item();
	}

	function update_cloi_from_values($values)
	{
		$cloi = $this->get_complex_learning_object_item();
		$cloi->set_is_homepage($values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]);
		return parent :: update_complex_learning_object_item();
	}

	// Inherited
	function update_complex_learning_object_item()
	{
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_is_homepage($values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]);
		return parent :: update_complex_learning_object_item();
	}
}

?>