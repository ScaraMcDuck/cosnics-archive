<?php

/*
 * This is the component that allows the user to make a wiki_page the homepage.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

class WikiDisplayWikiHomepageSetterComponent extends WikiDisplayComponent
{
	function run()
	{
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
