<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage description
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/description.class.php';
/**
 * A form to create/update a description
 */
class DescriptionForm extends LearningObjectForm
{
	// Inherited
	function create_learning_object()
	{
		$object = new Description();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>