<?php
/**
 * @package application.weblcms.tool.assessment.component
 */

require_once dirname(__FILE__).'/assessment_publication_table/assessment_publication_table.class.php';
require_once Path :: get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../../browser/learningobjectpublicationcategorytree.class.php';

/**
 * Represents the view component for the assessment tool.
 *
 */
class AssessmentToolViewerComponent extends AssessmentToolComponent
{
	private $action_bar;
    private $introduction_text;

	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'assessment');
		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications_new($condition);
		$this->introduction_text = $publications->next_result();

		$tree_id = WeblcmsManager :: PARAM_CATEGORY;
		$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		$this->set_parameter($tree_id, Request :: get($tree_id));

		$trail = new BreadCrumbTrail();
		$trail->add_help('courses assessment tool');
		$this->display_header($trail, true);

		$this->action_bar = $this->get_toolbar(true);

		if(PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			echo $this->display_introduction_text($this->introduction_text);
		}

		echo $this->action_bar->as_html();

		echo '<div style="width:18%; float: left; overflow: auto;">';

		echo $tree->as_html();

		echo '</div>';
		echo '<div style="width:80%; padding-left: 1%; float:right; ">';
		$table = new AssessmentPublicationTable($this, $this->get_user(), array('assessment', 'survey', 'hotpotatoes'), null);
		echo $table->as_html();
		echo '</div>';

		$this->display_footer();
	}
	
	function get_condition()
	{
		return null;
	}

	function get_toolbar($search)
	{
		$bar = parent :: get_toolbar($search);
		$bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		return $bar;
	}
}

?>