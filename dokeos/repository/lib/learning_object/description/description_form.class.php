<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/description.class.php';
/**
 * @package repository.learningobject
 * @subpackage description
 */
class DescriptionForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new Description();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>