<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once dirname(__FILE__).'/forumtable.class.php';

class ForumNodeDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$table = new ForumTable($object, $this->get_learning_object_url_format());
		$html = array();
		$html[] = parent :: get_full_html();
		$html[] = '<div class="lo_intermediate_header" style="margin: 1em 0 0.5em 0; font-weight: bold; font-size: larger;">'.htmlentities(Translation :: get($object->get_type() == 'forum' ? 'TopicsOnForum' : 'PostsInTopic')).'</div>';
		$html[] = $table->as_html();
		return implode("\n", $html);
	}
}
?>