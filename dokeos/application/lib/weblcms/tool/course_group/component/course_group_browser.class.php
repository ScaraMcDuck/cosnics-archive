<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/course_group_table/course_group_table.class.php';
require_once dirname(__FILE__).'/course_group_table/default_course_group_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/course_group_table/default_course_group_table_column_model.class.php';
require_once dirname(__FILE__).'/course_group_table/course_group_table_data_provider.class.php';

class CourseGroupToolBrowserComponent extends CourseGroupToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$this->action_bar = $this->get_action_bar();
		
		$course_group_table = new CourseGroupTable(new CourseGroupTableDataProvider($this));
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		
		echo '<br /><a name="top"></a>';
		echo $this->action_bar->as_html();
		echo $course_group_table->as_html();
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		//$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_img_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$param_add_course_group[Tool :: PARAM_ACTION] = CourseGroupTool :: ACTION_ADD_COURSE_GROUP;
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_img_path().'action_create.png', $this->get_url($param_add_course_group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_img_path().'action_edit.png', $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_img_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
	
	function get_condition()
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(Group :: PROPERTY_NAME, $query);
			$conditions[] = new LikeCondition(Group :: PROPERTY_DESCRIPTION, $query);
			return new OrCondition($conditions);
		}
		
		return null;
	}
}
?>