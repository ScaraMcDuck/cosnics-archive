<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__).'/../../course/course_user_category.class.php';
/**
 * Weblcms component which provides the user with a list
 * of all courses he or she has subscribed to.
 */
class WeblcmsManagerHomeComponent extends WeblcmsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyCourses')));
		
		$this->display_header($trail);
		echo '<div class="clear"></div><br />';
		$course_categories = $this->retrieve_course_user_categories(null, null, null, array(CourseUserCategory :: PROPERTY_SORT), array(SORT_ASC));

		echo $this->display_menu();
		
		//echo '<div class="maincontent">';
		echo '<div id="tool_browser_right">';
		
		$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, 0);
		$courses = $this->retrieve_courses($this->get_user_id(), $condition);

		echo $this->display_course_digest($courses);

		while ($course_category = $course_categories->next_result())
		{
			$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_CATEGORY, $course_category->get_id());
			$courses = $this->retrieve_courses($this->get_user_id(), $condition);
			echo $this->display_course_digest($courses, $course_category);
		}

		echo '</div>';

		$this->display_footer();
	}

	/*function display_menu()
	{
		$html = array();
		$html[] = '<div class="menu">';

		if ($this->get_user()->is_platform_admin())
		{
			$html[] = '<div class="menusection">';
			$html[] = '<span class="menusectioncaption">'.Translation :: get('CourseManagement').'</span>';
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
		$html[] = '<span class="menusectioncaption">'.Translation :: get('UserCourseManagement').'</span>';
		$html[] = '<ul class="menulist">';

		$html[] = $this->display_edit_course_list_links();

		$html[] = '</ul>';
		$html[] = '</div>';

		$html[] = '</div>';

		return implode($html, "\n");
	}*/
	
	function display_menu()
	{
		$html = array();
		
		$html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';
		
		$html[] = '<div id="tool_bar_hide_container" class="hide">';
		$html[] = '<a id="tool_bar_hide" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_right_hide.png" /></a>';
		$html[] = '<a id="tool_bar_show" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_right_show.png" /></a>';
		$html[] = '</div>';
		
		$html[] = '<div class="tool_menu">';
		$html[] = '<ul>';
		
		if ($this->get_user()->is_platform_admin())
		{
			$html[] = '<li class="tool_list_menu" style="font-weight: bold">' . Translation :: get('CourseManagement') . '</li><br />';
			$html[] = $this->display_platform_admin_course_list_links();
			$html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
		}
		else
		{
			$display_add_course_link = $this->get_user()->is_teacher() && ($_SESSION["studentview"] != "studentenview");
			if ($display_add_course_link)
			{
				$html[] = '<li class="tool_list_menu" style="font-weight: bold">' . Translation :: get('MenuUser') . '</li><br />';
				$html[] = $this->display_create_course_link();
			}
		}
		
		$html[] = '<li class="tool_list_menu" style="font-weight: bold">' . Translation :: get('UserCourseManagement') . '</li><br />';
		$html[] = $this->display_edit_course_list_links();
		$html[] = '</ul>';
		$html[] = '</div>';
		
		$html[] = '</div>';
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' .'"></script>';
		$html[] = '<div class="clear"></div>';

		return implode($html, "\n");
	}

	function display_create_course_link()
	{
		return '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_create.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE)) .'">'.Translation :: get('CourseCreate').'</a></li>';
	}
	function display_edit_course_list_links()
	{
		$html = array();
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_reset.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT)).'">'.Translation :: get('SortMyCourses').'</a></li>';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_subscribe.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SUBSCRIBE)).'">'.Translation :: get('CourseSubscribe').'</a></li>';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_unsubscribe.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_UNSUBSCRIBE)).'">'.Translation :: get('CourseUnsubscribe').'</a></li>';

		return implode($html, "\n");
	}

	function display_platform_admin_course_list_links()
	{
		$html = array();
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_create.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE)) .'">'.Translation :: get('CourseCreate').'</a></li>';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER)) .'">'.Translation :: get('CourseList').'</a></li>';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_move.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_COURSE_CATEGORY_MANAGER)) .'">'.Translation :: get('CourseCategoryManagement').'</a></li>';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_add.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSES)) .'">'.Translation :: get('ImportCourseCSV').'</a></li>';
		//$html[] = '<li><a href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE)) .'">'.Translation :: get('AddUserToCourse').'</a></li>';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_add.png)"><a style="top: -3px; position: relative;" href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSE_USERS)) .'">'.Translation :: get('ImportUsersForCourseCSV').'</a></li>';

		return implode($html, "\n");
	}

	function display_course_digest($courses, $course_category = null)
	{
		$html = array();
		if($courses->size() > 0)
		{
			$html[] = '<div class="block" id="block_courses" style="background-image: url('.Theme :: get_image_path('weblcms').'block_weblcms.png);">';
			$html[] = '<div class="title"><div style="float: left;">';
			
			if (isset($course_category))
			{
				//$html[] = '<ul class="user_course_category"><li>'.htmlentities($course_category->get_title()).'</li></ul>';
				$html[] = htmlentities($course_category->get_title());
			}
			else
			{
				$html[] = Translation :: get('GeneralCourses');
			}
			
			$html[] = '</div><a href="#" class="closeEl"><img class="visible" src="'.Theme :: get_common_image_path().'action_visible.png"/><img class="invisible" style="display: none;") src="'.Theme :: get_common_image_path().'action_invisible.png" /></a>';
			$html[] = '<div style="clear: both;"></div></div>';
			$html[] = '<div class="description">';
			
			$html[] = '<ul style="margin-left: -20px;">';
			while ($course = $courses->next_result())
			{
				$weblcms = $this->get_parent();
				$weblcms->set_course($course);
				$weblcms->load_tools();
				$tools = $weblcms->get_registered_tools();
				$html[] = '<li style="list-style: none; margin-bottom: 5px; list-style-image: url(' . Theme :: get_common_image_path() . 'action_home.png);"><a style="top: -2px; position: relative;" href="'. $this->get_course_viewing_url($course) .'">'.$course->get_name().'</a>';				
				/*$html[] = '<br />'. $course->get_id();
				
				$course_titular = $course->get_titular_string();
				if (!is_null($course_titular))
				{
					$html[] = ' - ' . $course_titular;
				}*/
				
				foreach($tools as $index => $tool)
				{					  
					if($tool->visible && $weblcms->tool_has_new_publications($tool->name))
					{
						$params[WeblcmsManager :: PARAM_TOOL] = $tool->name;
						$params[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
						$params[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE;
						$url = $weblcms->get_url($params);
						$html[] = '<a href="'.$url.'"><img src="'. Theme :: get_image_path(). 'tool_' . $tool->name.'_new.png" alt="'.Translation :: get('New').'"/></a>';
					}
				}
				
				$text = array();
				
				if(PlatformSetting :: get('display_course_code_in_title', 'weblcms'))
				{
					$text[] = $course->get_visual();
				}
				
				if(PlatformSetting :: get('display_teacher_in_title', 'weblcms'))
				{
					$text[] = UserDataManager :: get_instance()->retrieve_user($course->get_titular())->get_fullname();
				}
				
				if(PlatformSetting :: get('show_course_languages', 'weblcms'))
				{
					$text[] = ucfirst($course->get_language());
				}
				
				if(count($text) > 0)
				{
					$html[] = '<br />' . implode(' - ', $text);
				}
				
				$html[] = '</li>';
				$weblcms->set_course(null);
			}
			$html[] = '</ul>';
			
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<br />';
			$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/home_ajax.js' .'"></script>';
		
			if($_SESSION['toolbar_state'] == 'hide')
				$html[] = '<script type="text/javascript">var hide = "true";</script>';
			else
				$html[] = '<script type="text/javascript">var hide = "false";</script>';
		}
		return implode($html, "\n");
	}
}
?>