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

class WikiToolViewerComponent extends WikiToolComponent
{
	private $action_bar;
    private $publication_id; 
    private $cid;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        
        $this->display_header(new BreadcrumbTrail());
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
        
		$this->action_bar = $this->get_toolbar($wiki);
        echo '<br />' . $this->action_bar->as_html();
        if(!empty($wiki))
        {
            echo '<h2>' .$wiki->get_default_property('title') .' : '.Translation :: get('Home'). '</h2>';
            $table = new WikiPageTable($this, $wiki->get_id());
            echo $table->as_html();
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

    function get_toolbar($wiki)
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        
            $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );


		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('BrowseWikis'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('WikiStatistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_STATISTICS, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('wiki tool'));

        $action_bar->add_tool_action(ReportingManager :: get_access_details_toolbar_item());

		return $action_bar;
        
	}

    
}
?>
