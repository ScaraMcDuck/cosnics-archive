<?php
require_once dirname(__FILE__) . '/tool.class.php';

abstract class RepositoryTool extends Tool
{
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	function get_course_id()
	{
		return $this->get_parent()->get_course_id();
	}

	function get_groups()
	{
		return $this->get_parent()->get_groups();
	}

	function get_categories()
	{
		return $this->get_parent()->get_categories();
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
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					if($publication->delete())
					{
						$message = get_lang('LearningObjectPublicationDeleted');
					}
					break;
				case 'change_visibility':
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					$publication->change_visibility();
					if($publication->update())
					{
						$message = get_lang('LearningObjectPublicationVisibilityChanged');
					}
					break;
				case 'move_up':
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					if($publication->move_up())
					{
						$message = get_lang('LearningObjectPublicationMovedUp');
					}
					break;
				case 'move_down':
					$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
					if($publication->move_down())
					{
						$message = get_lang('LearningObjectPublicationMovedDown');
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