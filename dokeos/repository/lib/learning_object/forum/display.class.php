<?php
require_once dirname(__FILE__).'/../../treemenurenderer.class.php';
require_once dirname(__FILE__).'/../../learningobjecttree.class.php';
class ForumDisplay extends LearningObjectDisplay
{
	public function ForumDisplay(&$object)
	{
		parent :: LearningObjectDisplay($object);
	}
	public function get_full_html()
	{
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
		$html[] = '<div class="title">'.$object->get_title().'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div>';
		$html[] = '</div>';
		//$html[] = '<a href="create.php?category='.$object->get_id().'&amp;type=forum_topic" title="'.get_lang('Move').'">Create Topic</a><br />';
		$condition = new EqualityCondition('parent', $object->get_id());
		$datamanager = RepositoryDataManager :: get_instance();
		$objects = $datamanager->retrieve_learning_objects('forum_topic', $condition);
		$menu = new LearningObjectTree($object->get_id());
		$renderer =& new TreeMenuRenderer();
		$menu->render($renderer,'sitemap');
		$html[] = $renderer->toHtml();
		return implode("\n",$html);
	}
}
?>