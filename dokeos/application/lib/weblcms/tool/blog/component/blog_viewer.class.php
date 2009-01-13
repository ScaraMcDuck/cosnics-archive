<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';

class BlogToolViewerComponent extends BlogToolComponent
{
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
		

		$this->display_header($trail);

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
		$children = $rdm->retrieve_complex_learning_object_items($condition);
		
		while($child = $children->next_result())
		{
			$object_id = $child->get_ref();
			$object = $rdm->retrieve_learning_object($object_id);
			$html[] = $this->display_learning_object($object, $child->get_id());
		}
		
		return implode("\n", $html);
	}

}
?>