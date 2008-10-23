<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';

class CourseGroupToolSelfSubscriberComponent extends CourseGroupToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		$this->display_header($trail);
		
		$course_group = $this->get_course_group();
		$course_group->subscribe_users($this->get_user());
		$message = Display::display_normal_message(Translation :: get('UserSubscribed'),true);
	
		$this->display_footer();
	}

}
?>