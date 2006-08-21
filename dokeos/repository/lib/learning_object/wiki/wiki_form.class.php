<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/wiki.class.php';
/**
 * @package repository.learningobject
 * @subpackage wiki
 */
class WikiForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new Wiki();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>