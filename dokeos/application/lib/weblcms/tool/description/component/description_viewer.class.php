<?php

require_once dirname(__FILE__) . '/../description_tool.class.php';
require_once dirname(__FILE__) . '/../description_tool_component.class.php';
require_once dirname(__FILE__) . '/description_viewer/description_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class DescriptionToolViewerComponent extends DescriptionToolComponent
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
		
		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'description');
		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications_new($condition);
		$this->introduction_text = $publications->next_result();

		$this->action_bar = $this->get_action_bar();

		$browser = new DescriptionBrowser($this);
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses description tool');

        if(Request :: get('pid')!=null && Request :: get('tool_action')=='view')
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))),WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title()));
		$this->display_header($trail, true);

		echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		if(!Request :: get('pid'))
		{
			if(PlatformSetting :: get('enable_introduction', 'weblcms'))
			{
				echo $this->display_introduction_text($this->introduction_text);
			}
		}
        else
        {

        }
		echo $this->action_bar->as_html() . '<br />';
		echo '<div style="width:100%; float:right;">';
		echo $browser->as_html();
		echo '</div>';

		$this->display_footer();
	}

	function add_actionbar_item($item)
	{
		$this->action_bar->add_tool_action($item);
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		if(!Request :: get('pid'))
		{
			$action_bar->set_search_url($this->get_url());
			if($this->is_allowed(ADD_RIGHT))
			{
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(DescriptionTool :: PARAM_ACTION => DescriptionTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			}
		}

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms') && $this->is_allowed(EDIT_RIGHT))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}

		if($this->is_allowed(EDIT_RIGHT))
		{
			$action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
		}

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