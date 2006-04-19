<?php

/**
 * Renderer for displaying a list of publications
 */
class LearningObjectPublicationListRenderer
{

	var $tool;
	/**
	 * Create a new LearningObjectPublicationListRenderer
	 * @param RepositoryTool $tool
	 */
	function LearningObjectPublicationListRenderer($tool)
	{
		$this->tool = $tool;
	}
	/**
	 * Render a list of publications
	 * @param LearningObjectPublication[] $publications
	 * @return string
	 */
	function render($publications)
	{
		foreach ($publications as $index => $publication)
		{
			$first = $index == 0;
			$last = $index == count($publications) - 1;
			$html[] = $this->render_publication($publication, $first, $last);
		}
		return implode("\n", $html);
	}
	/**
	 * Render a publication
	 * @param LearningObjectPublication $publication
	 * @return string
	 */
	//TODO: split in more render-functions (render_action_buttons, render_publication_information,...) So more specialized renderers can easily be defined
	function render_publication($publication, $first = false, $last = false)
	{
		$html = array ();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$publication->get_learning_object()->get_type().'.gif" alt="'.$publication->get_learning_object()->get_type().'"/></div>';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = htmlentities($this->render_title($publication));
		$html[] = '</div>';
		$html[] = '<div class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_info'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_publication_information($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		$html[] = $this->render_publication_actions($publication,$first,$last);
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	/**
	 * Render the title of the publication
	 * @param LearningObjectPublication $publication
	 * @return string
	 */
	function render_title($publication)
	{
		return $publication->get_learning_object()->get_title();
	}
	/**
	 * Render the description of the publication
	 * @param LearningObjectPublication $publication
	 * @return string
	 */
	function render_description($publication)
	{
		return $publication->get_learning_object()->get_description();
	}
	/**
	 * Render the publication information
	 * @param LearningObjectPublication $publication
	 * @return string
	 */
	function render_publication_information($publication)
	{
		$users = $publication->get_target_users();
		$groups = $publication->get_target_groups();
		if (count($users) == 0 && count($groups) == 0)
		{
			$target_list = get_lang('Everybody');
		}
		else
		{
			$target_list = array ();
			$target_list[] = '<select>';
			foreach ($users as $index => $user_id)
			{
				$user = api_get_user_info($user_id);
				$target_list[] = '<option>'.$user['firstName'].' '.$user['lastName'].'</option>';
			}
			foreach ($groups as $index => $group_id)
			{
				//TODO: replace group id by group name (gives SQL-error now)
				//$group = GroupManager::get_group_properties($group_id);
				//$target_list[] = '<option>'.$group['name'].'</option>';
				$target_list[] = '<option>'.'GROUP: '.$group_id.'</option>';
			}
			$target_list[] = '</select>';
			$target_list = implode("\n", $target_list);
		}
		$publisher = api_get_user_info($publication->get_publisher_id());
		$html = array();
		//TODO: date-formatting
		$html[] = get_lang('PublishedOn').' '.date('r', $publication->get_publication_date());
		$html[] = get_lang('By').' '.$publisher['firstName'].' '.$publisher['lastName'].'. ';
		$html[] = get_lang('SentTo').': ';
		$html[] = $target_list;
		if (!$publication->is_forever())
		{
			//TODO: date-formatting
			$html[] = ' ('.get_lang('From').' '.date('r', $publication->get_from_date()).' '.get_lang('To').' '.date('r', $publication->get_to_date()).')';
		}
		return implode("\n",$html);
	}
	/**
	 * Render up-action
	 */
	function render_up_action($publication,$first = false)
	{
		if (!$first)
		{
			$up_img = 'up.gif';
			$up_url = $this->tool->get_url(array ('action' => 'move_up', 'pid' => $publication->get_id()), true);
			$up_link = '<a href="'.$up_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$up_img.'" alt=""/></a>';
		}
		else
		{
			$up_link = '<img src="'.api_get_path(WEB_CODE_PATH).'img/up_na.gif"  alt=""/></a>';
		}
		return $up_link;
	}
	/**
	 * Render down-action
	 */
	function render_down_action($publication,$last = false)
	{
		if (!$last)
		{
			$down_img = 'down.gif';
			$down_url = $this->tool->get_url(array ('action' => 'move_down', 'pid' => $publication->get_id()), true);
			$down_link = '<a href="'.$down_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$down_img.'"  alt=""/></a>';
		}
		else
		{
			$down_link = '<img src="'.api_get_path(WEB_CODE_PATH).'img/down_na.gif"  alt=""/></a>';
		}
		return $down_link;
	}
	/**
	 * Render visibility-action
	 */
	function render_visibility_action($publication)
	{
		$visibility_url = $this->tool->get_url(array ('action' => 'toggle_visibility', 'pid' => $publication->get_id()), true);
		$visibility_img = ($publication->is_hidden() ? 'invisible.gif' : 'visible.gif');
		$visibility_link = '<a href="'.$visibility_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$visibility_img.'"  alt=""/></a>';
		return $visibility_link;
	}
	/**
	 * Render edit-action
	 */
	function render_edit_action($publication)
	{
		$edit_url = $this->tool->get_url(array ('action' => 'edit', 'pid' => $publication->get_id()), true);
		$edit_link = '<a href="'.$edit_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif"  alt=""/></a>';
		return $edit_link;
	}
	/**
	 * Render delete-action
	 */
	function render_delete_action($publication)
	{
		$delete_url = $this->tool->get_url(array ('action' => 'delete', 'pid' => $publication->get_id()), true);
		$delete_link = '<a href="'.$delete_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif"  alt=""/></a>';
		return $delete_link;
	}
	/**
	 * Render publication actions
	 */
	function render_publication_actions($publication,$first,$last)
	{
		if ($this->tool->is_allowed(DELETE_RIGHT))
		{
			$html[] = $this->render_delete_action($publication);
		}
		if ($this->tool->is_allowed(EDIT_RIGHT))
		{
			$html[] = $this->render_edit_action($publication);
			$html[] = $this->render_visibility_action($publication);
			$html[] = $this->render_up_action($publication,$first);
			$html[] = $this->render_down_action($publication,$last);
		}
		return implode("\n",$html);
	}
}
?>