<?php
/**
 * $Id$
 * Description tool - list renderer
 * @package application.weblcms.tool
 * @subpackage description
 */
//require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/list_learning_object_publication_list_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/description/description.class.php';
/**
 * This class allows the end user to browse through published descriptions.
 */
class DescriptionBrowser extends LearningObjectPublicationBrowser
{
	/**
	 * Constructor
	 */
	function DescriptionBrowser($parent, $types)
	{
		parent :: __construct($parent, 'description');
		$renderer = new ListLearningObjectPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
		$actions = array(Tool :: ACTION_DELETE => Translation :: get('Delete selected'), 
						 Tool :: ACTION_HIDE => Translation :: get('Hide'), 
						 Tool :: ACTION_SHOW => Translation :: get('Show'));
		$renderer->set_actions($actions);
	}
	/*
	 * Inherited.
	 */
	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'description');
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
		$conditions[] = new EqualityCondition('type','announcement');
		if($this->get_parent()->get_condition())
			$conditions[] = $this->get_parent()->get_condition();
		$cond = new AndCondition($conditions);	
		
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $course_groups, $condition, false, array (LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC), 0, -1, null, $cond);
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
	/*
	 * Inherited.
	 */
	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>