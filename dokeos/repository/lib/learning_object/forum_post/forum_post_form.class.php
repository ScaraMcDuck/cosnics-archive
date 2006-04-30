<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/forum_post.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumPostForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new ForumPost();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>