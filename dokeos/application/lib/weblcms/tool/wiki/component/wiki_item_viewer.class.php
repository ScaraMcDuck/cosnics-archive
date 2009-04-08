<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolItemViewerComponent extends WikiToolComponent
{
	private $action_bar;
    private $object_id;
	

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        
        $this->object_id = Request :: get('object_id');
        
        $dm = RepositoryDataManager :: get_instance();
        $object_id = $dm->retrieve_learning_object($this->object_id);
        $this->display_header(new BreadcrumbTrail());
        
        $this->action_bar = $this->get_toolbar();
        echo '<br />' . $this->action_bar->as_html();

		
        echo '<h2>'.$object_id->get_title().'</h2>';
        echo $object_id->get_description();
        $this->display_footer();
	}

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);        

		$action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('View'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, 'object_id' => $this->object_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Create'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'object_id' => $this->object_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_EDIT_PAGE, 'object_id' => $this->object_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_DELETE, 'object_id' => $this->object_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Discuss'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_DISCUSS, 'object_id' => $this->object_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'object_id' => $this->object_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Notify Changes'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'object_id' => $this->object_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

		
		return $action_bar;
	}
}
?>