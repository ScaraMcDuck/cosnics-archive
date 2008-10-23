<?php

/**
 * This tool provides an interface for managing the course_groups in a course.
 */
class CourseGroupTool extends Tool
{

	/**
	 * The search form which can be used to search for users in the course_group tool.
	 */
	private $search_form;
	/**
	 * Runs this tool by performing the requested actions and showing the user
	 * interface.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$dm = WeblcmsDataManager :: get_instance();
		$course = $this->get_parent()->get_course();
		$course_groups = $dm->retrieve_course_groups($course->get_id());
		$param_add_course_group[Tool :: PARAM_ACTION] = self :: ACTION_ADD_COURSE_GROUP;
		$this->search_form = new CourseGroupToolSearchForm($this, $this->get_url());
		// We are inside a course_group area
		if (!is_null($this->get_parent()->get_course_group()))
		{
			$user_action = $_GET[Weblcms :: PARAM_USER_ACTION];
			$course_group_action = $_GET[self :: PARAM_COURSE_GROUP_ACTION];
			if ($user_action == UserTool :: USER_DETAILS)
			{
				$udm = UserDataManager :: get_instance();
				$user = $udm->retrieve_user($_GET[Weblcms :: PARAM_USERS]);
				$details = new UserDetails($user);
				$this->display_header($trail);
				echo $details->toHtml();
				$this->display_footer();
			}
			else
			{
				switch ($course_group_action)
				{
					
					default :
						
						break;
				}
			}
		}

	}

}
?>