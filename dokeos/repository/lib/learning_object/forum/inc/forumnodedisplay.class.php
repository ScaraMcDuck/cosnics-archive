<?php
require_once dirname(__FILE__).'/../../../treemenurenderer.class.php';
require_once dirname(__FILE__).'/forumtable.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumNodeDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$table = new ForumTable($object, $this->get_learning_object_url_format());
		$html = array();
		$html[] = parent :: get_full_html();
		$html[] = '<div class="lo_intermediate_header" style="margin: 1em 0 0.5em 0; font-weight: bold; font-size: larger;">'.htmlentities(get_lang($object->get_type() == 'forum' ? 'TopicsOnForum' : 'PostsInTopic')).'</div>';
		$html[] = $table->as_html();
		return implode("\n", $html);
	}
}
?>