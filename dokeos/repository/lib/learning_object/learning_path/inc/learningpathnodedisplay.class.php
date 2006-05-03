<?php
require_once dirname(__FILE__).'/../../../treemenurenderer.class.php';
require_once dirname(__FILE__).'/learningpathtable.class.php';
require_once dirname(__FILE__).'/learningpathtree.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathNodeDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$table = new LearningPathTable($object, $this->get_learning_object_url_format());
		return parent :: get_full_html().$table->as_html();
	}
}
?>