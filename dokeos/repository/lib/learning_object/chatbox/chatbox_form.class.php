<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/chatbox.class.php';
/**
 * @package repository.learningobject
 * @subpackage chatbox
 */
class ChatboxForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new Chatbox();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>