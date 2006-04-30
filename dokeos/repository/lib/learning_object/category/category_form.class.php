<?php
/**
 * @package repository.learningobject
 * @subpackage category
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/category.class.php';
class CategoryForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new Category();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>