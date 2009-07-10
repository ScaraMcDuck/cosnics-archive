<?php
/**
 * $Id$
 * Announcement tool - browser
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/announcement/announcement.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/description/description.class.php';
/**
 * Browser to allow the user to view the published announcements
 */
class AnnouncementBrowser extends LearningObjectPublicationBrowser
{
	/**
	 * @see LearningObjectPublicationBrowser::LearningObjectPublicationBrowser()
	 */
	private $publications;

	function AnnouncementBrowser($parent)
	{
		parent :: __construct($parent, 'announcement');
		if(Request :: get('pid') && $parent->get_action() == 'view')
		{
			$this->set_publication_id(Request :: get('pid'));
			$parent->set_parameter(Tool :: PARAM_ACTION, AnnouncementTool :: ACTION_VIEW_ANNOUNCEMENTS);
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$renderer = new ListLearningObjectPublicationListRenderer($this);
			$actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'),
						 Tool :: ACTION_HIDE => Translation :: get('Hide'),
						 Tool :: ACTION_SHOW => Translation :: get('Show'));
			$renderer->set_actions($actions);
		}

		$this->set_publication_list_renderer($renderer);
	}
	/**
	 * Retrieves the publications
	 * @return array An array of LearningObjectPublication objects
	 */
	function get_publications($from, $count, $column, $direction)
	{
		if(empty($this->publications))
		{
			$datamanager = WeblcmsDataManager :: get_instance();
			$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'announcement');
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
			$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $course_groups, $condition, false, new ObjectTableOrder(Announcement :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC), array (), 0, -1, null, $cond);
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
			$this->publications = $visible_publications;
		}

		return $this->publications;

	}
	/**
	 * Retrieves the number of published annoucements
	 * @return int
	 */
	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>