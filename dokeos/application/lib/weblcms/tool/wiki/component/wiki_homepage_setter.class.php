<?php

/*
 * This is the component that allows the user to make a wiki_page the homepage.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';
require_once Path::get_repository_path().'/lib/data_manager/database.class.php';
require_once Path::get_repository_path().'/lib/learning_object/wiki_page/complex_wiki_page.class.php';

class WikiToolHomepageSetterComponent extends WikiToolComponent
{
	function run()
	{
        if (!$this->is_allowed(EDIT_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();

        $dm = RepositoryDataManager :: get_instance();
        $page = $dm->retrieve_complex_learning_object_item(Request :: get('cid'));
        /*
         *  If the wiki_page isn't empy the homepage will be set
         */
        if(!empty($page))
        {
            $page->set_is_homepage(true);
            $page->update();
        }
        $this->redirect(null, '', array(Tool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, 'pid' => Request :: get('pid')));

    }
}

?>
