<?php

require_once dirname(__FILE__) . '/../glossary_tool.class.php';
require_once dirname(__FILE__) . '/../glossary_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class GlossaryDisplayGlossaryPublisherComponent extends GlossaryDisplayComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => GlossaryTool :: ACTION_PUBLISH)), Translation :: get('Publish')));
        $trail->add_help('courses glossary tool');

		$object = $_GET['object'];
		$pub = new LearningObjectRepoViewer($this, 'glossary', true);

		if(!isset($object))
		{
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'LearningObject: ';
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}

		$this->display_header($trail, true);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>