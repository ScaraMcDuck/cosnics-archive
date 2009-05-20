<?php

require_once dirname(__FILE__) . '/../announcement_tool.class.php';
require_once dirname(__FILE__) . '/../announcement_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class AnnouncementToolPublisherComponent extends AnnouncementToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
        
		$object = $_GET['object'];
		$pub = new LearningObjectRepoViewer($this, 'announcement', true);
		
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
		
		$this->display_header($trail, true, 'courses announcement tool');
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>