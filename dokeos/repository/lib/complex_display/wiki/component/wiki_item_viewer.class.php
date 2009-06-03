<?php

/*
 * This viewer will show the selected wiki_page.
 * You'll be redirected here from the wiki_viewer page by clicking on the name of a wiki_page
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

//require_once dirname(__FILE__) . '/../wiki_tool.class.php';
//require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once dirname(__FILE__).'/wiki_parser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiDisplayWikiItemViewerComponent extends WikiDisplayComponent
{
	private $action_bar;
    private $publication_id;
    private $cid;
    private $wid;
    private $wiki_page;
    private $links;


	function run()
	{
        /*
         * publication and complex object id are requested.
         * These are used to retrieve
         *  1) the complex object ( reference is stored )
         *  2) the learning object ( actual inforamation about a wiki_page is stored here )
         *
         */
        $this->publication_id = Request :: get('pid');
        $this->cid = Request :: get('cid');
        $dm = RepositoryDataManager :: get_instance();
        $this->wid = (RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($this->cid)->get_parent());
        $this->links = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->wid)->get_links();

       /*
        *  If a complex object id is passed, the object will be retrieved
        */
        if(!empty($this->cid))
        {
            $cloi = $dm->retrieve_complex_learning_object_item($this->cid);
            $this->wiki_page = $dm->retrieve_learning_object($cloi->get_ref());
        }

        $trail = new BreadcrumbTrail();
        $trail->add_help('courses wiki tool');
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $this->publication_id)), DokeosUtilities::truncate_string(WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication($this->publication_id)->get_learning_object()->get_title(),20)));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->publication_id, Tool :: PARAM_COMPLEX_ID => $this->cid)), DokeosUtilities::truncate_string($this->wiki_page->get_title(),20)));
        

        $this->display_header($trail, true);

        $this->action_bar = $this->get_toolbar();
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.$this->wiki_page->get_title().'</div><hr style="height:1px;color:#4271B5;width:100%;">';

        /*
         *  Here we create the wiki_parser component.
         *  For more information about the parser, please read the information provided in the wiki_parser class
         */
		$parser = new WikiToolParserComponent(Request :: get('pid'),$this->get_parent()->get_parent()->get_course_id(),$this->wiki_page->get_description(),$this->cid);
        echo $parser->parse_wiki_text();
        echo $parser->get_wiki_text();
        /*
         * If you don't want the bottom link to show, put the next line in comment
         */
        echo '<div ><a href=#top>'.'back to top'.'</a></div>';
        echo '</div>';
	}

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_WIKI);

		$action_bar->set_search_url($this->get_url());

        //PAGE ACTIONS
        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => 'view', WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE_CLOI, 'pid' => $this->publication_id,'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Discuss'), Theme :: get_common_image_path().'action_users.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DISCUSS, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
        /*$action_bar->add_common_action(
        new ToolbarItem(
				Translation :: get('BrowseWikis'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			));*/

         $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('BrowseWiki'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        //INFORMATION
        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_versions.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        /*$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('NotifyChanges'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/


        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Statistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay ::ACTION_PAGE_STATISTICS, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        //NAVIGATION
        if(!empty($this->links))
        {
            $p = new WikiToolParserComponent($this->publication_id,$this->get_parent()->get_parent()->get_course()->get_id(),$this->links);
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
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'title' =>$p->get_title_from_wiki_tag($this->links[$i],false))), ToolbarItem :: DISPLAY_ICON_AND_LABEL,null,'does_not_exist'
                    ));
                }
                else
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), Tool :: PARAM_COMPLEX_ID =>$p->get_cid_from_url($link) )), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                    ));
                }
                $i++;
            }
        }

		return $action_bar;
	}
}
?>