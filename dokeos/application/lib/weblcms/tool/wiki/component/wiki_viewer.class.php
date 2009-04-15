<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
	private $action_bar;
    private $publication_id;
    private $wiki_id;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}        
        $this->display_header(new BreadcrumbTrail());
        $dm = RepositoryDataManager :: get_instance();
        $this->publication_id = Request :: get('pid');
        $this->wiki_id = Request :: get('wiki_id');
        if(!empty($this->publication_id))
        {           
            $wm = WeblcmsDataManager :: get_instance();
            $publication = $wm->retrieve_learning_object_publication($this->publication_id);
            $this->wiki_id = $publication->get_learning_object()->get_id();
            $wiki = $dm->retrieve_learning_object($this->wiki_id);
        }
        elseif(!empty($this->wiki_id))
        {
            $wiki = $dm->retrieve_learning_object($this->wiki_id);
            
        }
		$this->action_bar = $this->get_toolbar();
        echo '<br />' . $this->action_bar->as_html();
        if(isset($wiki))
        {
            echo '<h2>' .$wiki->get_default_property('title') .' : Home </h2>';
            $table = new WikiPageTable($this, $wiki->get_id());
            echo $table->as_html();
        }
        $this->display_footer();
	}

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if(!WikiTool ::is_wiki_locked($this->wiki_id))
        {
            $action_bar->set_search_url($this->get_url());
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );
        }

		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        /*$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('DeleteContents'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_DELETE_WIKI_CONTENTS, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('WikiStatistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_STATISTICS, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('wiki tool'));
		return $action_bar;
	}
}
?>