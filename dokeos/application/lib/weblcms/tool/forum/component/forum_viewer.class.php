<?php

require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class ForumToolViewerComponent extends ForumToolComponent
{
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $cid = Request :: get(Tool :: PARAM_COMPLEX_ID);
		$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);

        $this->display_header(new BreadcrumbTrail());

        $cd = ComplexDisplay :: factory($this);
        $cd->run();

        $this->display_footer();

        switch($cd->get_action())
        {
            case ForumDisplay :: ACTION_VIEW_TOPIC:
                Events :: trigger_event('view_forum_topic', 'weblcms', array('user_id' => $this->get_user_id(), 'publication_id' => $pid,
								'forum_topic_id' => $cid));
                break;
        }
    }

	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
	{
        $parameters[Tool :: PARAM_ACTION] = ForumTool::ACTION_VIEW_FORUM;
		return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
	}

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
	{
        $parameters[Tool :: PARAM_ACTION] = ForumTool::ACTION_VIEW_FORUM;
		$this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
	}
}
?>
