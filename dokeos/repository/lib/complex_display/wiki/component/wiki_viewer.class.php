<?php

/*
 * This is the compenent that allows the user to view all pages of a wiki.
 * If no homepage is set all available pages will be shown, otherwise the homepage will be shown.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/wiki_parser.class.php';
require_once Path :: get_repository_path().'lib/complex_display/wiki/wiki_display.class.php';

class WikiDisplayWikiViewerComponent extends WikiDisplayComponent
{
	private $action_bar;
    private $links;
    private $current_wiki;

	function run()
	{

        $dm = RepositoryDataManager :: get_instance();    

        $this->current_wiki = $dm->retrieve_learning_object(Request :: get('pid'));

        $trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $this->current_wiki->get_id())), DokeosUtilities::truncate_string($this->current_wiki->get_title(),20)));
        $trail->add_help('courses wiki tool');
        $this->get_parent()->get_parent()->display_header($trail, true);

        $this->links = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->current_wiki->get_id())->get_links();

		$this->action_bar = $this->get_toolbar($this->current_wiki);
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        if(!empty($this->current_wiki))
        {
            echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.$this->current_wiki->get_title().'</div><hr style="height:1px;color:#4271B5;width:100%;">';
            $table = new WikiPageTable($this, $this->current_wiki->get_id());
            echo $table->as_html().'</div>';
        }
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
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(ComplexDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, 'pid' => $this->current_wiki->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, WikiDisplay :: PARAM_DISPLAY_ACTION => null, 'pid' => $this->current_wiki->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE, WikiDisplay :: PARAM_DISPLAY_ACTION => null, 'pid' => $this->current_wiki->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
			)
		);

        $action_bar->add_common_action(
        new ToolbarItem(
				Translation :: get('BrowseWikis'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS, WikiDisplay :: PARAM_DISPLAY_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			));


        //INFORMATION
        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('WikiStatistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_STATISTICS, 'pid' => $this->current_wiki->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
        $action_bar->add_tool_action($this->get_parent()->get_parent()->get_access_details_toolbar_item($this));

        //NAVIGATION

        if(!empty($this->links))
        {
            $p = new WikiToolParserComponent($this->current_wiki->get_id(),$this->get_parent()->get_parent()->get_course()->get_id(),$this->links);
            $toolboxlinks = $p->handle_toolbox_links($this->links);
            $this->links = explode(';',$this->links);
            $i=0;

            foreach($toolboxlinks as $link)
            {
                if(substr_count($link,'www.')==1)
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        ucfirst($p->get_title_from_url($link)), null, $link, ToolbarItem ::DISPLAY_LABEL));
                    continue;
                }
                
                if(substr_count($link,'class="does_not_exist"'))
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'title' =>$p->get_title_from_wiki_tag($this->links[$i],false))), ToolbarItem :: DISPLAY_ICON_AND_LABEL,null,'does_not_exist'
                    ));
                }
                else
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), Tool :: PARAM_COMPLEX_ID =>$p->get_cid_from_url($link) )), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                    ));
                }
                $i++;
            }
        }

		return $action_bar;
	}


}
?>
