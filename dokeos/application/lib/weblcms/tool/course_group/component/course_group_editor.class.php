<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__).'/../../../course_group/course_group_form.class.php';

class CourseGroupToolEditorComponent extends CourseGroupToolComponent
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
		$course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
		$wdm = WeblcmsDataManager :: get_instance();
		$course_group = $wdm->retrieve_course_group($course_group_id);

		$form = new CourseGroupForm(CourseGroupForm :: TYPE_EDIT, $course_group, $this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_EDIT_COURSE_GROUP, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)));
		if ($form->validate())
		{
			$form->update_course_group();
			$this->redirect(Translation :: get('CourseGroupUpdated'));
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