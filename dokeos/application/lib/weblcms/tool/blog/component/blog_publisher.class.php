<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class BlogToolPublisherComponent extends BlogToolComponent
{
	function run() 
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('Publisher')));
		
		$object = $_GET['object'];
		$pub = new LearningObjectRepoViewer($this, 'blog_item', true);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url(array(BlogTool :: PARAM_ACTION => BlogTool :: ACTION_VIEW_BLOGS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}
		
		$this->display_header($trail);
		
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>