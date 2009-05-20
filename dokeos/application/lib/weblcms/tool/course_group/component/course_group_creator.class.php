<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__).'/../../../course_group/course_group_form.class.php';

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
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_ADD_COURSE_GROUP)), Translation :: get('Create')));
		$course = $this->get_course();
		$course_group = new CourseGroup(null, $course->get_id());
		$param_add_course_group[Tool :: PARAM_ACTION] = CourseGroupTool :: ACTION_ADD_COURSE_GROUP;
		$form = new CourseGroupForm(CourseGroupForm :: TYPE_CREATE, $course_group, $this->get_url($param_add_course_group));
		if ($form->validate())
		{
			$form->create_course_group();
			$this->get_parent()->redirect(Translation :: get('CourseGroupCreated'));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}

	}

}
?>