<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__).'/../../../course_group/course_group_subscriptions_form.class.php';

class CourseGroupToolManageSubscriptionsComponent extends CourseGroupToolComponent
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
		$course_group = $this->get_course_group();

		$form = new CourseGroupSubscriptionsForm($course_group, $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_MANAGE_SUBSCRIPTIONS, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group->get_id())), $this);
		if ($form->validate())
		{
			$succes = $form->update_course_group_subscriptions();

			if($succes)
				$message = 'CourseGroupSubscriptionsUpdated';
			else
				$message = 'MaximumAmountOfMembersReached';

			$this->redirect(Translation :: get($message));
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