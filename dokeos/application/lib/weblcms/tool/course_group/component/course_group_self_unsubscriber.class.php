<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';

class CourseGroupToolSelfUnsubscriberComponent extends CourseGroupToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$course_group = $this->get_course_group();
		$course_group->unsubscribe_users($this->get_user());
		$this->redirect(null, Translation :: get('UserUnsubscribed'), '', array());
	}

}
?>