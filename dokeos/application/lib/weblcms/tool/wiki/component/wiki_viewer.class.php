<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
	private $action_bar;
	private $introduction_text;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        $this->display_header(new BreadcrumbTrail());
        $this->action_bar = $this->get_toolbar();
        echo '<br />' . $this->action_bar->as_html();
        $publication_id = Request :: get('pid');
        $wm = WeblcmsDataManager :: get_instance();
        $dm = RepositoryDataManager :: get_instance();
        $publication = $wm->retrieve_learning_object_publication($publication_id);
        $object_id = $publication->get_learning_object()->get_id();
        
        $wiki = $dm->retrieve_learning_object($object_id);      
		
        echo '<h2>Title : ' .$wiki->get_default_property('title') .'</h2>';
        
        /*
         *  check which pages are linked to this wiki, loop them and show the name (basic)
         */
        $table = new WikiPageTable($this, $wiki->get_id());
		echo $table->as_html();
        
        $this->display_footer();
	}

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
//dump($this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE)));
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Create'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('wiki tool'));
		return $action_bar;
	}
}
?>