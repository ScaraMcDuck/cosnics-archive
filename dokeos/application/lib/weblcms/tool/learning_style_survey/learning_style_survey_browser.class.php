<?php
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/learning_style_survey_publication_list_renderer.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyBrowser extends LearningObjectPublicationBrowser
{
	function LearningStyleSurveyBrowser($parent)
	{
		parent :: __construct($parent, 'learning_style_survey_profile');
		$renderer = new LearningStyleSurveyPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'learning_style_survey');
		$condition = $tool_condition;
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$course_groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$course_groups = $this->get_course_groups();
		}
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $course_groups, $condition, false, array (Announcement :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC));
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}
		return $visible_publications;
	}

	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>