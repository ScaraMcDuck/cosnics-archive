<?php
/**
 * Renderer for displaying a list of publications
 */
class LearningObjectPublicationListRenderer {

	var $tool;
    function LearningObjectPublicationListRenderer($tool)
    {
    	$this->tool = $tool;
    }
    function render($publications)
    {
    	foreach($publications as $index => $publication)
    	{
    		$first = $index == 0;
    		$last = $index == count($publications)-1;
    		$html[] = $this->render_publication($publication,$first,$last);
    	}
    	return implode("\n",$html);
    }
    /**
     *
     */
    //TODO: split in more render-functions (render_action_buttons, render_publication_information,...) So more specialized renderers can easily be defined
    function render_publication($publication,$first = false, $last = false)
    {

  		$learning_object = $publication->get_learning_object();
		$target_users = $publication->get_target_users();
		$delete_url = $this->tool->get_url(array('action'=>'delete','pid'=>$publication->get_id()), true);
		$edit_url = $this->tool->get_url(array('action'=>'edit','pid'=>$publication->get_id()), true);
		$visible_url = $this->tool->get_url(array('action'=>'toggle_visibility','pid'=>$publication->get_id()), true);
		if(!$first)
		{
			$up_img = 'up.gif';
			$up_url = $this->tool->get_url(array('action'=>'move_up','pid'=>$publication->get_id()), true);
			$up_link = '<a href="'.$up_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$up_img.'" alt=""/></a>';
		}
		else
		{
			$up_link = '<img src="'.api_get_path(WEB_CODE_PATH).'img/up_na.gif"  alt=""/></a>';
		}
		if(!$last)
		{
			$down_img = 'down.gif';
			$down_url = $this->tool->get_url(array('action'=>'move_down','pid'=>$publication->get_id()), true);
			$down_link = '<a href="'.$down_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$down_img.'"  alt=""/></a>';
		}
		else
		{
			$down_link = '<img src="'.api_get_path(WEB_CODE_PATH).'img/down_na.gif"  alt=""/></a>';
		}
		$visibility_img = ($publication->is_hidden() ? 'invisible.gif' : 'visible.gif');
		$users = $publication->get_target_users();
		$groups = $publication->get_target_groups();
		if(count($users) == 0 && count($groups) == 0)
		{
			$target_list = get_lang('Everybody');
		}
		else
		{
			$target_list = array();
			$target_list[] = '<select>';
			foreach($users as $index => $user_id)
			{
				$user = api_get_user_info($user_id);
				$target_list[] = '<option>'.$user['firstName'].' '.$user['lastName'].'</option>';
			}
			foreach($groups as $index => $group_id)
			{
				//TODO: replace group id by group name (gives SQL-error now)
				//$group = GroupManager::get_group_properties($group_id);
				//$target_list[] = '<option>'.$group['name'].'</option>';
				$target_list[] = '<option>'.'GROUP: '.$group_id.'</option>';
			}
			$target_list[] = '</select>';
			$target_list = implode("\n",$target_list);
		}
		$publisher = api_get_user_info($publication->get_publisher_id());


    	$html = array();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$publication->get_learning_object()->get_type().'.gif" alt="'.$publication->get_learning_object()->get_type().'"/></div>';
	    $html[] = '<div class="title'.($publication->is_visible_for_target_users() ? '':' invisible').'">';
    	$html[] = htmlentities($this->render_title($publication));
		$html[] = '</div>';
    	$html[] = '<div class="description'.($publication->is_visible_for_target_users() ? '':' invisible').'">';
    	$html[] = $this->render_description($publication);
    	$html[] = '</div>';
    	$html[] = '<div class="publication_info'.($publication->is_visible_for_target_users() ? '':' invisible').'">';
		//TODO: date-formatting
		$html[] = get_lang('PublishedOn').' '.date('r',$publication->get_publication_date());
		$html[] = get_lang('By').' '.$publisher['firstName'].' '.$publisher['lastName'].'. ';
		$html[] = get_lang('SentTo').': ';
		$html[] = $target_list;
		if(!$publication->is_forever())
		{
			//TODO: date-formatting
			$html[] = ' ('.get_lang('From').' '.date('r',$publication->get_from_date()).' '.get_lang('To').' '.date('r',$publication->get_to_date()).')';
		}
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		if($this->tool->is_allowed(DELETE_RIGHT))
		{
			$html[] = '<a href="'.$delete_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif"  alt=""/></a>';
		}
		if($this->tool->is_allowed(EDIT_RIGHT))
		{
			$html[] = '<a href="'.$edit_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif"  alt=""/></a>';
			$html[] = '<a href="'.$visible_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$visibility_img.'"  alt=""/></a>';
			$html[] = $up_link;
			$html[] = $down_link;
		}
		$html[] = '</div>';
		$html[] = '</div>';
    	return implode("\n",$html);
    }
    function display($publication)
    {
		echo $this->render($publication);
    }
	function render_title($publication)
	{
		return $publication->get_learning_object()->get_title();
	}
	function render_description($publication)
	{
		return $publication->get_learning_object()->get_description();
	}
}
?>