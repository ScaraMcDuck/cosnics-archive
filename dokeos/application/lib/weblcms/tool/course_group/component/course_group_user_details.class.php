<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';

class CourseGroupToolCreatorComponent extends CourseGroupToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		$this->display_header($trail);
		
	
		$this->display_footer();
	}

}
?>