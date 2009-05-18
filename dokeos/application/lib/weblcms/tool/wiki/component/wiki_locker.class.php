<?php

/*
 * This is the component that allows the user to lock a wiki.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolLockerComponent extends WikiToolComponent
{
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        /*
         *  Publication id is needed to retrieve the wiki which the users wants to lock.
         */
        $conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT,0);
        $conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF,Request :: get('pid'));
        $conditions = new AndCondition($conditions);
        $wiki = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($conditions)->as_array();
        /*
         *  If the wiki is locked he will be unlocked, otherwise the wiki will be locked
         */
        if($wiki[0]->get_is_locked())
        {
            $wiki[0]->set_is_locked(false);
        }
        else
        {
            $wiki[0]->set_is_locked(true);
        }
        $wiki[0]->update();
        $this->redirect(null, '', array(Tool :: PARAM_ACTION => WikiTool ::ACTION_BROWSE_WIKIS));
	}
}
?>