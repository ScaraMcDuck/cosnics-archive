<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}


        $this->action_bar = $this->get_toolbar();

        $cd = ComplexDisplay :: factory($this, 'wiki');
        $cd->run();
    }

	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
	{
        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
		return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
	}

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
	{
        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
		$this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
	}

     function get_condition()
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_TITLE, $query);
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_DESCRIPTION, $query);
			return new OrCondition($conditions);
		}
		return null;
	}

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_WIKI);

		$action_bar->set_search_url($this->get_url());

        //PAGE ACTIONS
        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
			)
		);

        $action_bar->add_common_action(
        new ToolbarItem(
				Translation :: get('BrowseWikis'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			));


        //INFORMATION
        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('WikiStatistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_STATISTICS, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
        $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));

        /*$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('NotifyChanges'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/


        //NAVIGATION

        if(!empty($this->links))
        {
            $p = new WikiToolParserComponent($this->publication_id,$this->get_course()->get_id(),$this->links);
            $toolboxlinks = $p->handle_toolbox_links($this->links);
            $this->links = explode(';',$this->links);
            $i=0;

            foreach($toolboxlinks as $link)
            {
                if(substr_count($link,'class="does_not_exist"'))
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool ::ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'title' =>$p->get_title_from_wiki_tag($this->links[$i],false))), ToolbarItem :: DISPLAY_ICON_AND_LABEL,null,'does_not_exist'
                    ));
                }
                else
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), Tool :: PARAM_COMPLEX_ID =>$p->get_cid_from_url($link) )), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                    ));
                }
                $i++;
            }
        }


		return $action_bar;
	}
}
?>
