<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage category
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/category.class.php';
/**
 * A form to create/update a category
 */
class CategoryForm extends LearningObjectForm
{
	function setCsvValues($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];	
		parent :: setValues($defaults);
			
	}

	//Inherited
	function create_learning_object()
	{
		$object = new Category();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

}
?>
