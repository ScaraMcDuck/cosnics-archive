<?php
/**
 * $Id: wikibrowser.class.php 9206 2006-09-05 10:12:59Z bmol $
 * Chat tool - list renderer
 * @package application.weblcms.tool
 * @subpackage chat
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/chat_publication_list_renderer.class.php';
/**
 * This class allows the end user to browse through published chatboxes.
 */
class ChatBrowser extends LearningObjectPublicationBrowser
{
	/**
	 * Constructor
	 */
	function ChatBrowser($parent, $types)
	{
		parent :: __construct($parent, 'chat');
		$renderer = new ChatPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}
	/*
	 * Inherited.
	 */
	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'chat');
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
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $course_groups, $condition, false, array ('display_order'), array (SORT_DESC), 0, -1, null, $this->get_parent()->get_condition());
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