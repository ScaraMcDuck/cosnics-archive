<?php

/*
 * This is the compenent that allows the user to view all pages of a wiki.
 * If no homepage is set all available pages will be shown, otherwise the homepage will be shown.
 * 
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/wiki_parser.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
	private $action_bar;
    private $publication_id; 
    private $cid;
    private $links;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        
        $dm = RepositoryDataManager :: get_instance();

        /*
         * publication and complex object id are requested.
         * These are used to retrieve
         *  1) the complex object ( reference is stored )
         *  2) the learning object ( actual inforamation about a wiki_page is stored here )
         *
         */
        
        $this->publication_id = Request :: get('pid');
        $this->cid = Request :: get('cid');

        /*
         *  If the publication id isn't empty the publication will be retrieved.
         *  This controle make sure that
         *      1)the retrieve learning object publication is valid
         *      2)the method get_id() is only called when the publication object is made.
         */
        if(!empty($this->publication_id))
        {           
            $wm = WeblcmsDataManager :: get_instance();
            $publication = $wm->retrieve_learning_object_publication($this->publication_id);
            if(isset($publication))
                $this->wiki_id = $publication->get_learning_object()->get_id();
            $wiki = $dm->retrieve_learning_object($this->wiki_id);
        }

        $_SESSION['wiki_title'] = $publication->get_learning_object()->get_title();
        $trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $this->publication_id)), $publication->get_learning_object()->get_title()));
        $this->display_header($trail);

        $this->links = explode(';',RepositoryDataManager :: get_instance()->retrieve_learning_object($this->wiki_id)->get_links());
		$this->action_bar = $this->get_toolbar($wiki);
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        if(!empty($wiki))
        {
            echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.$wiki->get_default_property('title').'</div><hr style="height:1px;color:#4271B5;width:100%;">';
            $table = new WikiPageTable($this, $wiki->get_id());
            echo $table->as_html().'</div>';
        }
        $this->display_footer();
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
        
        /*$action_bar->add_common_action(
        new ToolbarItem(
				Translation :: get('BrowseWikis'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			));*/
        

        //INFORMATION
        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('WikiStatistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_STATISTICS, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
        $action_bar->add_tool_action(ReportingManager :: get_access_details_toolbar_item($this));
        $action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('wiki tool'));

        /*$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('NotifyChanges'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/
        

        //NAVIGATION
        $p = new WikiToolParserComponent();

        if(!empty($this->links[0]))
        {
            foreach($this->links as $link)
            {
                if(substr_count($link,'class="does_not_exist"'))
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_url($link), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool ::ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link))), ToolbarItem :: DISPLAY_ICON_AND_LABEL,null,'does_not_exist'
                    ));
                }
                else
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_url($link), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), Tool :: PARAM_COMPLEX_ID =>$p->get_cid_from_url($link) )), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                    ));
                }
            }
        }


		return $action_bar;
	}

    
}
?>
