<?php
require_once dirname(__FILE__).'/../../treemenurenderer.class.php';
require_once dirname(__FILE__).'/../../learningobjecttree.class.php';
class ForumPostDisplay extends LearningObjectDisplay
{
	public function ForumPostDisplay(&$object)
	{
		parent :: LearningObjectDisplay($object);
	}
	public function get_full_html()
	{
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object">';
		$html[] = '<div style="float:right;width:80%;">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';		
		$html[] = '<div class="title">'.$object->get_title().'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div></div>';
		$type_array = array('forum', 'forum_topic', 'forum_post');
		$menu = new LearningObjectTree($object->get_parent_id(),$type_array);
		$renderer =& new TreeMenuRenderer();
		$menu->render($renderer,'sitemap');
		$html[] = '<div>'.$renderer->toHtml().'</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>