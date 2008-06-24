<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_component.class.php';
require_once dirname(__FILE__).'/../../course/course_user_category.class.php';
/**
 * Weblcms component which provides the user with a list
 * of all courses he or she has subscribed to.
 */
class WeblcmsHomeComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyCourse')));
		
		$this->display_header($trail);
		$course_categories = $this->retrieve_course_user_categories(null, null, null, array(CourseUserCategory :: PROPERTY_SORT), array(SORT_ASC));

		echo '<div class="maincontent">';
		$courses = $this->retrieve_courses($this->get_user_id(), 0);
		echo $this->display_course_digest($courses);

		while ($course_category = $course_categories->next_result())
		{
			$courses = $this->retrieve_courses($this->get_user_id(), $course_category->get_id());
			echo $this->display_course_digest($courses, $course_category);
		}

		echo '</div>';

		echo $this->display_menu();

		$this->display_footer();
	}

	function display_menu()
	{
		$html = array();
		$html[] = '<div class="menu">';

		if ($this->get_user()->is_platform_admin())
		{
			$html[] = '<div class="menusection">';
			$html[] = '<span class="menusectioncaption">'.Translation :: get('MenuAdmin').'</span>';
			$html[] = '<ul class="menulist">';
			$html[] = $this->display_platform_admin_course_list_links();
			$html[] = '</ul>';
			$html[] = '</div>';
		}
		else
		{
			$display_add_course_link = $this->get_user()->is_teacher() && ($_SESSION["studentview"] != "studentenview");
			if ($display_add_course_link)
			{
				$html[] = '<div class="menusection">';
				$html[] = '<span class="menusectioncaption">'.Translation :: get('MenuUser').'</span>';
				$html[] = '<ul class="menulist">';
				$html[] = $this->display_create_course_link();
				$html[] = '</ul>';
				$html[] = '</div>';
			}
		}

		$html[] = '<div class="menusection">';
		$html[] = '<span class="menusectioncaption">'.Translation :: get('CourseManagement').'</span>';
		$html[] = '<ul class="menulist">';

		$html[] = $this->display_edit_course_list_links();

		$html[] = '</ul>';
		$html[] = '</div>';

		$html[] = '</div>';

		return implode($html, "\n");
	}

	function display_create_course_link()
	{
		return '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_CREATE_COURSE)) .'">'.Translation :: get('CourseCreate').'</a></li>';
	}
	function display_edit_course_list_links()
	{
		$html = array();
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_MANAGER_SORT)).'">'.Translation :: get('SortMyCourses').'</a></li>';
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_MANAGER_SUBSCRIBE)).'">'.Translation :: get('CourseSubscribe').'</a></li>';
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_MANAGER_UNSUBSCRIBE)).'">'.Translation :: get('CourseUnsubscribe').'</a></li>';

		return implode($html, "\n");
	}

	function display_platform_admin_course_list_links()
	{
		$html = array();
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_CREATE_COURSE)) .'">'.Translation :: get('CourseCreate').'</a></li>';
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_IMPORT_COURSES)) .'">'.Translation :: get('CourseCreateCsv').'</a></li>';
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_COURSE_CATEGORY_MANAGER)) .'">'.Translation :: get('CourseCategoryManagement').'</a></li>';
		//$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_CREATE_COURSE)) .'">'.Translation :: get('AddUserToCourse').'</a></li>';
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_IMPORT_COURSE_USERS)) .'">'.Translation :: get('AddUserToCourseCsv').'</a></li>';
		$html[] = '<li><a href="'.$this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_ADMIN_COURSE_BROWSER)) .'">'.Translation :: get('CourseList').'</a></li>';

		return implode($html, "\n");
	}

	function display_course_digest($courses, $course_category = null)
	{
		$html = array();
		if($courses->size() > 0)
		{
			if (isset($course_category))
			{
				$html[] = '<ul class="user_course_category"><li>'.htmlentities($course_category->get_title()).'</li></ul>';
			}
			$html[] = '<ul>';
			while ($course = $courses->next_result())
			{
				$weblcms = $this->get_parent();
				$weblcms->set_course($course);
				$weblcms->load_tools();
				$tools = $weblcms->get_registered_tools();
				$html[] = '<li><a href="'. $this->get_course_viewing_url($course) .'">'.$course->get_name().'</a>';				
				$html[] = '<br />'. $course->get_id();
				
				$course_titular = $course->get_titular_string();
				if (!is_null($course_titular))
				{
					$html[] = ' - ' . $course_titular;
				}
				
				foreach($tools as $index => $tool)
				{					  
					if($tool->visible && $weblcms->tool_has_new_publications($tool->name))
					{
						$params[Weblcms::PARAM_TOOL] = $tool->name;
						$params[Weblcms::PARAM_COURSE] = $course->get_id();
						$params[Weblcms::PARAM_ACTION] = Weblcms::ACTION_VIEW_COURSE;
						$url = $weblcms->get_url($params);
						$html[] = '<a href="'.$url.'"><img src="'. Theme :: get_img_path(). 'tool_' . $tool->name.'_new.png" alt="'.Translation :: get('New').'"/></a>';
					}
				}
				$html[] = '</li>';
				$weblcms->set_course(null);
			}
			$html[] = '</ul>';
		}
		return implode($html, "\n");
	}
}
?>