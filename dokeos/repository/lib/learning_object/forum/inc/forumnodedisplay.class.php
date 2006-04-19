<?php
require_once dirname(__FILE__).'/../../../treemenurenderer.class.php';
require_once dirname(__FILE__).'/forumtable.class.php';
require_once dirname(__FILE__).'/forumtree.class.php';
/**
 * @package repository.learningobject.forum
 */
class ForumNodeDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$menu = new ForumTree($object->get_id(), $object->get_id());
		$renderer =& new TreeMenuRenderer();
		$menu->render($renderer,'sitemap');
		$html[] = '<div class="forum_tree" style="float:left;width:20%;">';
		$html[] = $renderer->toHtml();
		$html[] = '</div>';
		$html[] = '<div class="forum_topics" style="float:right;width:80%;">';
		$html[] = parent :: get_full_html();
		$table = new ForumTable($object);
		$html[] = $table->as_html();
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>