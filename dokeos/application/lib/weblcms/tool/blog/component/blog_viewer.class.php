<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/blog_viewer/blog_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class BlogToolViewerComponent extends BlogToolComponent
{
	private $action_bar;
	
	function run() 
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$pid = Request :: get('pid');

		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','blog'),false, null, null, 0, -1, null, new EqualityCondition('type','introduction'));
		$this->introduction_text = $publications->next_result();
		
		$this->action_bar = $this->get_action_bar();
		
		$browser = new BlogBrowser($this);
        $trail = new BreadcrumbTrail();

        if($browser->get_publication_category_tree()!=null)
        {
            $breadcrumbs = $browser->get_publication_category_tree()->get_breadcrumbs();
            unset($breadcrumbs[0]);
            foreach($breadcrumbs as $breadcrumb)
            {
                $trail->add(new BreadCrumb($breadcrumb['url'], $breadcrumb['title']));
            }
            $_SESSION['breadcrumbs'] = $trail->get_breadcrumbs();
        }
        
        //needed when viewing a blog item, to access the breadcrumbs of the categories
        if(Request :: get('tool_action') == 'view')
        $trail->set_breadcrumbtrail($_SESSION['breadcrumbs']);

        if(Request :: get('pid')!=null)
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title()));

        $this->display_header($trail);
		
		//echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		if(!isset($pid))
		{
			if(PlatformSetting :: get('enable_introduction', 'weblcms'))
			{
				echo $this->display_introduction_text($this->introduction_text);
			}
		}
		echo $this->action_bar->as_html();
		echo '<div id="action_bar_browser">';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
	
	function add_actionbar_item($item)
	{
		$this->action_bar->add_tool_action($item);
	}
	
	function get_action_bar($pid)
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		if(!$pid)
		{
			$action_bar->set_search_url($this->get_url());
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		if(!$pid)
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('blog tool'));

        $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));

		return $action_bar;
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

    private function get_menu_field_by_index(&$menu, $index)
    {
        foreach (array_keys($menu) as $key) {
            if ($key == $index) {
                return $menu[$key];
            } elseif (!empty($menu[$key]['sub']) && '' != ($field = $this->get_menu_field_by_index($menu[$key]['sub'], $index))) {
                return $field;
            }
        }
        return '';
    }
	
	/*function display_introduction_text()
	{
		$html = array();
		
		$introduction_text = $this->introduction_text;
		
		if($introduction_text)
		{
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);
			
			$html[] = '<div class="learning_object">';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_learning_object()->get_description();
			$html[] = '</div>';
			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}
		
		return implode("\n",$html);
	}*/

}
?>