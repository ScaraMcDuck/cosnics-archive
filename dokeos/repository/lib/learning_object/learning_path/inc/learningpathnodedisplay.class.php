<?php
require_once dirname(__FILE__).'/../../../treemenurenderer.class.php';
require_once dirname(__FILE__).'/learningpathtable.class.php';
require_once dirname(__FILE__).'/learningpathtree.class.php';
/**
 * @package repository.learningobject.learning_path
 */
class LearningPathNodeDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$menu = new LearningPathTree($object->get_id(), $object->get_id());
		$renderer =& new TreeMenuRenderer();
		$menu->render($renderer,'sitemap');
		$html[] = '<div class="forum_tree" style="float:left;width:20%;">';
		$html[] = $renderer->toHtml();
		$html[] = '</div>';
		$html[] = '<div class="forum_topics" style="float:right;width:80%;">';
		$html[] = parent :: get_full_html();
		$table = new LearningPathTable($object);
		$html[] = $table->as_html();
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>