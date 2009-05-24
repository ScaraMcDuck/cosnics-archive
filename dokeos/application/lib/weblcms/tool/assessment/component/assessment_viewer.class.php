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

		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','assessment'),false, null, null, 0, -1, null, new EqualityCondition('type','introduction'));
		$this->introduction_text = $publications->next_result();

		$tree_id = WeblcmsManager :: PARAM_CATEGORY;
		$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		$this->set_parameter($tree_id, $_GET[$tree_id]);

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

	function get_toolbar($search)
	{
		$bar = parent :: get_toolbar($search);
		$bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		return $bar;
	}
}

?>