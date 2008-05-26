<?php
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/learning_path_chapter.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathChapterForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new LearningPathChapter();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>