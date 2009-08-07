<?php

require_once dirname(__FILE__) . '/../geolocation_tool.class.php';
require_once dirname(__FILE__) . '/../geolocation_tool_component.class.php';
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class GeolocationToolPublisherComponent extends GeolocationToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
        if(Request :: get('pcattree') > 0)
        {
            foreach(Tool ::get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
            {
                $trail->add(new BreadCrumb($this->get_url(), $breadcrumb->get_name()));
            }
        }
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => GeolocationTool :: ACTION_PUBLISH)), Translation :: get('Publish')));
        $trail->add_help('courses geolocation tool');

		$object = Request :: get('object');
		$pub = new LearningObjectRepoViewer($this, 'physical_location', true);

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