<?php
/**
 * $Id$
 * Forum tool - topic list renderer
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/table_learning_object_publication_list_renderer.class.php';

class ForumTopicListRenderer extends TableLearningObjectPublicationListRenderer
{
    function ForumTopicListRenderer($browser)
    {
    	parent :: __construct($browser);
    	$column = 0;
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++,'',false);
    	}
		$this->set_header($column++, Translation :: get('Topics'), false);
		$this->set_header($column++, Translation :: get('Replies'), false);
		$this->set_header($column++, Translation :: get('Author'), false);
		$this->set_header($column++, Translation :: get('LastPost'), false);
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++,'',false);
    	}
    }
    function render_delete_action($topic)
	{
		$delete_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, 'topic' => $topic->get_id()), true);
		$delete_link = '<a href="'.$delete_url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_image_path().'action_delete.png"  alt=""/></a>';
		return $delete_link;
	}
    function render_lock_action($topic)
	{
		if($topic->is_locked())
		{
			$action = 'unlock';
			$img = 'action_lock.png';
		}
		else
		{
			$action = 'lock';
			$img = 'action_unlock.png';
		}
		$url = $this->get_url(array (Tool :: PARAM_ACTION => $action, 'topic_id' => $topic->get_id()), true);
		$link = '<a href="'.$url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_image_path().$img.'"  alt=""/></a>';
		return $link;
	}
	// Inherited
	function render_publication_actions($topic,$first,$last)
	{
		$html = array();
		$html[] = '<span style="white-space: nowrap;">';
		$this->set_parameter('topic',$topic->get_id());
		if ($this->is_allowed(DELETE_RIGHT))
		{
			$html[] = $this->render_delete_action($topic);
		}
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$html[] = $this->render_lock_action($topic);
			//$html[] = $this->render_edit_action($topic);
			//$html[] = $this->render_visibility_action($topic);
			//$html[] = $this->render_move_to_category_action($publication,$last);
			//$html[] = $this->render_reply_action($topic);
		}
		$html[] = '</span>';
		return implode($html);
	}
}
?>