<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class BlogToolPublisherComponent extends BlogToolComponent
{
	function run()
	{
		/*if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}*/

		$trail = new BreadcrumbTrail();

        if(Request :: get('tool')=='blog' && isset($_SESSION['blog_breadcrumbs']))
        {
            $breadcrumbs = $_SESSION['blog_breadcrumbs'];
            foreach($breadcrumbs as $breadcrumb)
            {
                $trail->add(new BreadCrumb($breadcrumb['url'], $breadcrumb['title']));
            }
        }
		$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), Translation :: get('Publisher')));
		$trail->add_help('courses blog tool');

		$object = Request :: get('object');
		$pub = new LearningObjectRepoViewer($this, 'blog_item', true);

		if(!isset($object))
		{
			$html[] =  $pub->as_html();
		}
		else
		{
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}

		$this->display_header($trail, true);

		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>