<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';

class WikiToolContentsDeleterComponent extends WikiToolComponent
{
    /*private $wiki_id;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        $dm = RepositoryDataManager :: get_instance();
        $this->wiki_id = Request :: get('wiki_id');
        $condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->wiki_id);
        $pages = $dm->retrieve_complex_wiki_pages($condition);
        foreach($pages as $page)
        {
            $page->delete();
        }
        $this->redirect($message, '', array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, 'wiki_id' => $this->wiki_id));
	}*/
}
?>