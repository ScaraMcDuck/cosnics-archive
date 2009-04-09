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
        $conditions[] = new EqualityCondition(ComplexWikipage ::PROPERTY_REF, Request :: get('ref'));
        $conditions[] = new EqualityCondition(ComplexWikipage ::PROPERTY_PARENT, Request :: get('parent'));
        $page = $dm->retrieve_complex_learning_object_items(new AndCondition($conditions))->as_array();
        if(!empty($page[0]))
        {
            $page[0]->set_is_homepage(true);
            $page[0]->update();
        }
        $this->redirect(null, null, '', array(Tool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, WikiTool :: PARAM_PUBLICATION_ID => Request :: get('parent')));
    }
}

?>
