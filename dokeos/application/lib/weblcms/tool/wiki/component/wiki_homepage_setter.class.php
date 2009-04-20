<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';
require_once Path::get_repository_path().'/lib/data_manager/database.class.php';
require_once Path::get_repository_path().'/lib/learning_object/wiki_page/complex_wiki_page.class.php';

class WikiToolHomepageSetterComponent extends WikiToolComponent
{
    private $pub;
	function run()
	{
        if (!$this->is_allowed(EDIT_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        
		$trail = new BreadcrumbTrail();

        $dm = RepositoryDataManager :: get_instance();
        $page = $dm->retrieve_complex_learning_object_item(Request :: get('id'));
        if(!empty($page))
        {
            $page->set_is_homepage(true);
            $page->update();
        }
        $this->redirect(null, null, '', array(Tool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, 'cid' => Request :: get('id')));
        
    }
}

?>
