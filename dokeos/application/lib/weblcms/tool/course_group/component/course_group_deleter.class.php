<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../course_group/course_group.class.php';

class CourseGroupToolDeleterComponent extends CourseGroupToolComponent
{
	
	function run()
	{
		if(!$this->is_allowed(DELETE_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$ids = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
		if($ids)
		{
			if(!is_array($ids))
				$ids = array($ids);
			
			foreach($ids as $group_id)
			{
				$cg = new CourseGroup();
				$cg->set_id($group_id);
				$cg->delete();
			}
			
			$message = Translation :: get('CourseGroupsDeleted');
			$this->redirect(null, $message, '', array('course_group' => null));	
			
		}
		else
		{
			Display :: display_error_message('NoObjectSelected');
		}
		
	}

}
?>