<?php
/**
 * $Id: announcementbrowser.class.php 9148 2006-08-23 07:46:27Z bmol $
 * Announcement tool - browser
 * @package application.weblcms.tool.exercise.component
 * @subpackage exercise_viewer
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/exercise_publication_list_renderer.class.php';
require_once Path :: get_repository_path().'lib/learning_object/exercise/complex_exercise.class.php';
require_once Path :: get_repository_path().'lib/learning_object/exercise/exercise.class.php';

class ExerciseBrowser extends LearningObjectPublicationBrowser
{
	function ExerciseBrowser($parent, $types)
	{
		parent :: __construct($parent, 'exercise');
		$renderer = new ExercisePublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'exercise');
		$condition = $tool_condition;
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$groups = $this->get_groups();
		}
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $groups, $condition, false, array (ComplexExercise :: PROPERTY_DISPLAY_ORDER), array (SORT_ASC));
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
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