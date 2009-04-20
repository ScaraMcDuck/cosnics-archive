<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolItemViewerComponent extends WikiToolComponent
{
	private $action_bar;
    private $wiki_page_id;
    private $wiki_id;
    private $cid;
    private $wiki_page;
	

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        $this->wiki_page_id = Request :: get('wiki_page_id');
        $this->wiki_id = Request :: get('wiki_id');
        $this->cid = Request :: get('cid');        
        $dm = RepositoryDataManager :: get_instance();
        if(isset($this->wiki_page_id))
            $this->wiki_page = $dm->retrieve_learning_object($this->wiki_page_id);
        elseif(isset($this->cid))
        {
            $cloi = $dm->retrieve_complex_learning_object_item($this->cid);
            $this->wiki_page_id = $cloi->get_ref();
            $this->wiki_page = $dm->retrieve_learning_object($this->wiki_page_id);
        }

        /*
         * complex object id needed to delete / update
         */
              
        if(!isset($this->cid))
        {
            $condition = New EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->wiki_page->get_id());
            $cloi = $dm->retrieve_complex_learning_object_items($condition)->as_array();
            $this->cid = $cloi[0]->get_id();
        }        

        $this->display_header(new BreadcrumbTrail());
        $this->action_bar = $this->get_toolbar();

        echo '<br />' . $this->action_bar->as_html();

		
        echo '<h2>'.$this->wiki_page->get_title().'</h2>';
        echo $this->wiki_page->get_description();
        $this->display_footer();
	}

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);        

		$action_bar->set_search_url($this->get_url());


        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('BrowseWiki'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);        

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE_CLOI, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Discuss'), Theme :: get_common_image_path().'action_users.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_DISCUSS, 'wiki_page_id' => $this->wiki_page_id, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_versions.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'wiki_page_id' => $this->wiki_page_id, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('NotifyChanges'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'wiki_page_id' => $this->wiki_page_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Statistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_PAGE_STATISTICS, 'wiki_page_id' => $this->wiki_page_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

		
		return $action_bar;
	}
}
?>