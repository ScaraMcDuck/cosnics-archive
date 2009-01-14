<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';

class BlogToolViewerComponent extends BlogToolComponent
{
	private $action_bar;
	
	function run() 
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		
		$pid = $_GET['pid'];
		if(!$pid)
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
		}
		
		$trail->add(new BreadCrumb($this->get_url(array('pid' => $pid)), Translation :: get('ViewBlog')));
		
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_learning_object_publication($pid);
		$root_object = $publication->get_learning_object();
			
		$this->action_bar = $this->get_toolbar($root_object->get_id());

		$this->display_header($trail);
		
		echo '<br />' . $this->action_bar->as_html();

		echo '<h3>' . $root_object->get_title() . '</h3>';
		echo $root_object->get_description();
		echo '<br /><br />';
		
		echo $this->display_children($root_object);

		$this->display_footer();
	}
	
	function display_children($root_object)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $root_object->get_id());
		$children = $rdm->retrieve_complex_learning_object_items($condition, array(ComplexLearningObjectItem :: PROPERTY_DISPLAY_ORDER), array(SORT_DESC));
		
		while($child = $children->next_result())
		{
			$object_id = $child->get_ref();
			$object = $rdm->retrieve_learning_object($object_id);
			$html[] = $this->display_learning_object($object, $child->get_id());
		}
		
		return implode("\n", $html);
	}
	
	function get_toolbar($parent_id) 
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		if($this->is_allowed(ADD_RIGHT))
		{
			$action_bar->add_common_action(
				new ToolbarItem(
					Translation :: get('CreatePost'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(BlogTool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, 'parent' => $parent_id, 'type' => 'blog_item')), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				)
			);
		}
		
		return $action_bar;
	}

}
?>