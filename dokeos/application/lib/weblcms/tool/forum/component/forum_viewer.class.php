<?php

require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class ForumToolViewerComponent extends ForumToolComponent
{
	private $action_bar;
	private $introduction_text;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
	
		$this->display_header(new BreadcrumbTrail());
		
		$this->display_footer();
	}
}
?>