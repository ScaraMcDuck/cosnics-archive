<?php
/**
 * @package repository.learningobject
 * @subpackage feedback
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/feedback.class.php';
/**
 * A form to create/update a feedback
 */
class FeedbackForm extends LearningObjectForm
{
	// Inherited
	function create_learning_object()
	{
		$object = new Feedback();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>