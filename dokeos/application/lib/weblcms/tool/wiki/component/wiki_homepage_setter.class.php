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
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();

        $dm = RepositoryDataManager :: get_instance();
        /*
         * First get the old homepage, and deactivate its 'homepage' status.
         */
        $conditions[0] = new EqualityCondition(ComplexWikiPage :: PROPERTY_PARENT, Request :: get('parent'));
        $conditions[1] = new EqualityCondition(ComplexWikiPage :: PROPERTY_IS_HOMEPAGE, true);
        //$condition = new AndCondition($conditions);
        $page = $dm->retrieve_complex_wiki_page(new AndCondition($conditions));
        if(!empty($page))
        {
            $page->set_is_homepage(false);
            $page->update();
        }


        /*
         * Then retrieve the page that is to be the new homepage and activate its 'homepage' status.
         */

        $condition = new EqualityCondition('repository_complex_wiki_page.'.ComplexWikipage :: PROPERTY_ID, Request :: get('pid'));
        $page = $dm->retrieve_complex_wiki_page($condition);
        if(!empty($page))
        {
            $page->set_is_homepage(true);
            $page->update();
        }
        $this->redirect(null, null, '', array(Tool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, WikiTool :: PARAM_PUBLICATION_ID => Request :: get('parent')));
    }
}

?>
