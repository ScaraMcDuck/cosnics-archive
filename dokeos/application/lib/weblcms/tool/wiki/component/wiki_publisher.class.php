<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class WikiToolPublisherComponent extends WikiToolComponent
{
	function run() 
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();		
		$object = $_GET['object'];
       
		$pub = new LearningObjectRepoViewer($this, 'wiki', true);
	
		if(!isset($object))
		{  
			$html[] = '<p><a href="' . $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
            $wiki = ComplexLearningObjectItem ::factory('wiki',array('ref' => $object,'parent' => 0, 'user_id' => $this->get_user_id(), 'display_order' => RepositoryDataManager :: get_instance()->select_next_display_order($object)),array('is_locked' => false));
            $wiki->create();
		}
		
		$this->display_header($trail);
		
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>