<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$this->display_header(new breadcrumbTrail());
        $cd = ComplexDisplay :: factory($this, 'wiki');

        $cd->run();
        $this->display_footer();
    }
}
?>
