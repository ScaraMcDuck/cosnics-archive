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
	private $introduction_text;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','course_group'),false, null, null, 0, -1, null, new EqualityCondition('type','introduction'));
		$this->introduction_text = $publications->next_result();

		$this->action_bar = $this->get_action_bar();

		$course_group_table = new CourseGroupTable(new CourseGroupTableDataProvider($this));
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses group');

		$this->display_header($trail, true);

		echo '<br /><a name="top"></a>';

		if(PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			echo $this->display_introduction_text();
		}

		echo $this->action_bar->as_html();
		echo $course_group_table->as_html();
		$this->display_footer();
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		//$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$param_add_course_group[Tool :: PARAM_ACTION] = CourseGroupTool :: ACTION_ADD_COURSE_GROUP;
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path().'action_create.png', $this->get_url($param_add_course_group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!$this->introduction_text)
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}

		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		return $action_bar;
	}

	function get_condition()
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(CourseGroup :: PROPERTY_NAME, $query);
			$conditions[] = new LikeCondition(CourseGroup :: PROPERTY_DESCRIPTION, $query);
			return new OrCondition($conditions);
		}

		return null;
	}

	function display_introduction_text()
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
	}
}
?>