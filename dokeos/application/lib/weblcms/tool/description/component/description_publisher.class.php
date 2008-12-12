<?php

require_once dirname(__FILE__) . '/../description_tool.class.php';
require_once dirname(__FILE__) . '/../description_tool_component.class.php';
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class DescriptionToolPublisherComponent extends DescriptionToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		/*$pub = new LearningObjectPublisher($this, 'description', true);
		
		$html[] = '<p><a href="' . $this->get_url(array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();*/
		
		$object = $_GET['object'];
		$pub = new LearningObjectRepoViewer($this, 'description', true);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url(array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'LearningObject: ';
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}
		
		$this->display_header($trail);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>