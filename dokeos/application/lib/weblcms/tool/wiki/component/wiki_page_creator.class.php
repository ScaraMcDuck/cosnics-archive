<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';

class WikiToolPageCreatorComponent extends WikiToolComponent
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
		$pub = new LearningObjectRepoViewer($this, 'wiki_page', true,RepoViewer :: SELECT_MULTIPLE, WikiTool ::ACTION_CREATE_PAGE);

		if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			$cloi = new ComplexLearningObjectItem(array(ComplexLearningObjectItem :: PROPERTY_REF,$object));
		}

		$this->display_header($trail);
        //$page = $pub->get_default_learning_object('wiki_page');
        //dump($page);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>