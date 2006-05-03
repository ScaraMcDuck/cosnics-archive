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
		$html = array();
		$html[] = parent :: get_full_html();
		$html[] = '<div class="lo_intermediate_header" style="margin: 1em 0 0.5em 0; font-weight: bold; font-size: larger;">'.get_lang($object->get_type() == 'learning_path' ? 'ChaptersInLearningPath' : 'ItemsInChapter').'</div>';
		$html[] = $table->as_html();
		return implode("\n", $html);
	}
}
?>