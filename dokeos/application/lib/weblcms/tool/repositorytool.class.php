<?php
/**
 * Repository tool
 * @package application.weblcms.tool
 */
require_once dirname(__FILE__) . '/tool.class.php';

/**
==============================================================================
 *	This is the base class for all tools used in applications that use the
 *	repository. It offers additional repository-related functionality.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class RepositoryTool extends Tool
{
	/**
	 * @see Application :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	/**
	 * @see Application :: get_course_id()
	 */
	function get_course_id()
	{
		return $this->get_parent()->get_course_id();
	}

	/**
	 * @see Application :: get_groups()
	 */
	function get_groups()
	{
		return $this->get_parent()->get_groups();
	}

	/**
	 * @see Application :: get_categories()
	 */
	function get_categories($list = false)
	{
		return $this->get_parent()->get_categories($list);
	}

	/**
	 * @see Application :: get_category()
	 */
	function get_category($id)
	{
		return $this->get_parent()->get_category($id);
	}

	/**
	 * Handles requests like deleting a publication, changing display order of
	 * publication, etc.
	 * The action and the necessary parameters are retrieved from the query
	 * string. This function also displays a message box with the result of the
	 * action.
	 */
	 // TODO: add some input validation to check if the requested action can be performed
	function perform_requested_actions()
	{
		if(isset($_GET['action']))
		{
			$datamanager = WebLCMSDataManager :: get_instance();
			switch($_GET['action'])
			{
				case 'delete':
					if($this->is_allowed(DELETE_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						if($publication->delete())
						{
							$message = get_lang('LearningObjectPublicationDeleted');
						}
					}
					break;
				case 'toggle_visibility':
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						$publication->toggle_visibility();
						if($publication->update())
						{
							$message = get_lang('LearningObjectPublicationVisibilityChanged');
						}
					}
					break;
				case 'move_up':
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						if($publication->move(-1))
						{
							$message = get_lang('LearningObjectPublicationMoved');
						}
					}
					break;
				case 'move_down':
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						if($publication->move(1))
						{
							$message = get_lang('LearningObjectPublicationMoved');
						}
					}
					break;
			}
		}
		if(isset($message))
		{
			Display::display_normal_message($message);
		}
	}
}
?>