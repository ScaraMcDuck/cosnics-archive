<?php

require_once dirname(__FILE__) . '/../glossary_tool.class.php';
require_once dirname(__FILE__) . '/../glossary_tool_component.class.php';
require_once dirname(__FILE__) . '/glossary_browser/glossary_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/glossary/glossary.class.php';

class GlossaryToolBrowserComponent extends GlossaryToolComponent
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
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'glossary');
		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications_new($condition);
		$this->introduction_text = $publications->next_result();

		$this->action_bar = $this->get_action_bar();

		$browser = new GlossaryBrowser($this);
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses glossary tool');

		$this->display_header($trail, true);

		//echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		if(!Request :: get('pid'))
		{
			if(PlatformSetting :: get('enable_introduction', 'weblcms'))
			{
				echo $this->display_introduction_text($this->introduction_text);
			}
		}
		echo $this->action_bar->as_html();
		echo $browser->as_html();

		$this->display_footer();
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		if(!Request :: get('pid'))
		{
			$action_bar->set_search_url($this->get_url());
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(GlossaryTool :: PARAM_ACTION => GlossaryTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
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
}
?>