<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once dirname(__FILE__).'/learningpathtable.class.php';


class LearningPathNodeDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$html = array();
		$is_chapter = ($object->get_type() == 'learning_path_chapter');
		$table = new LearningPathTable($object, $this->get_learning_object_url_format(), true);
		$html[] = parent :: get_full_html();
		$html[] = self :: intermediate_header(Translation :: get('ChaptersIn' . ($is_chapter ? 'Chapter' : 'LearningPath')));
		$html[] = $table->as_html();
		if ($is_chapter)
		{
			$table = new LearningPathTable($object, $this->get_learning_object_url_format(), false);
			$html[] = parent :: get_full_html();
			$html[] = self :: intermediate_header(Translation :: get('ItemsInChapter'));
			$html[] = $table->as_html();
		}
		return implode("\n", $html);
	}

	private static function intermediate_header ($title)
	{
		return '<div class="lo_intermediate_header" style="margin: 1em 0 0.5em 0; font-weight: bold; font-size: larger;">'.htmlentities($title).'</div>';
	}
}
?>