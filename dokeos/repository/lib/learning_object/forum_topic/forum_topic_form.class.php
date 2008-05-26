<?php
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/forum_topic.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumTopicForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new ForumTopic();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>