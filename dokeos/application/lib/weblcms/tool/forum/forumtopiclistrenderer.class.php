<?php
/**
 * $Id$
 * Forum tool - topic list renderer
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/tablelearningobjectpublicationlistrenderer.class.php';

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
		$this->set_header($column++, Translation :: get_lang('Topics'), false);
		$this->set_header($column++, Translation :: get_lang('Replies'), false);
		$this->set_header($column++, Translation :: get_lang('Author'), false);
		$this->set_header($column++, Translation :: get_lang('LastPost'), false);
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++,'',false);
    	}
    }
    function render_delete_action($topic)
	{
		$delete_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_DELETE, 'topic' => $topic->get_id()), true);
		$delete_link = '<a href="'.$delete_url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get_lang('ConfirmYourChoice'))).'\');"><img src="'.$this->browser->get_path(WEB_IMG_PATH).'delete.gif"  alt=""/></a>';
		return $delete_link;
	}
    function render_lock_action($topic)
	{
		if($topic->is_locked())
		{
			$action = 'unlock';
			$img = 'lockthread.gif';
		}
		else
		{
			$action = 'lock';
			$img = 'unlock.gif';
		}
		$url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => $action, 'topic_id' => $topic->get_id()), true);
		$link = '<a href="'.$url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get_lang('ConfirmYourChoice'))).'\');"><img src="'.$this->browser->get_path(WEB_IMG_PATH).$img.'"  alt=""/></a>';
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