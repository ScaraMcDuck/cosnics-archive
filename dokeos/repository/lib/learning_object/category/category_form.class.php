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
	//Inherited
	function create_learning_object()
	{
		$object = new Category();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>