<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class LearningPathToolPublisherComponent extends LearningPathToolComponent
{
	function run()
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$trail->add_help('courses learnpath tool');

		$object = $_GET['object'];
		$pub = new LearningObjectRepoViewer($this, 'learning_path', true);

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