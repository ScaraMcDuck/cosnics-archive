<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
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
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('MyCourses'));
		$this->display_header($breadcrumbs);
		$course_categories = $this->retrieve_course_user_categories($this->get_user_id());
		$courses = $this->retrieve_courses($this->get_user_id(), 0);
		
		echo '<div class="maincontent">';
		$courses = $this->retrieve_courses($this->get_user_id(), 0);
		echo '<ul>';
		while ($course = $courses->next_result())
		{
			echo '<li><a href="'. $this->get_course_viewing_url($course) .'">'.$course->get_name().'</a><br />'. $course->get_id() .' - '. $course->get_titular() .'</li>';
		}
		echo '</ul>';
		
		while ($course_categorie = $course_categories->next_result())
		{
			echo '<ul class="user_course_category"><li>'.$course_categorie->get_title().'</li></ul>';
			echo '<ul>';
			$courses = $this->retrieve_courses($this->get_user_id(), $course_categorie->get_id());
			while ($course = $courses->next_result())
			{
				echo '<li><a href="'. $this->get_course_viewing_url($course) .'">'.$course->get_name().'</a><br />'. $course->get_id() .' - '. $course->get_titular() .'</li>';
			}
			echo '</ul>';
		}
		
		echo '</div>';
		
		$this->display_menu();
		
		$this->display_footer();
	}
	
	function display_menu()
	{
		/*
		 ==============================================================================
		 RIGHT MENU
		 ==============================================================================
		 */
		echo '<div class="menu">';
		
		//api_display_language_form(); // moved to the profile page.
		echo '<div class="menusection">';
		echo '<span class="menusectioncaption">'.get_lang('MenuUser').'</span>';
		echo '<ul class="menulist">';
		
		$display_add_course_link = api_is_allowed_to_create_course() && ($_SESSION["studentview"] != "studentenview");
		if ($display_add_course_link)
		{
			$this->display_create_course_link();
		}
		$this->display_edit_course_list_links();
		
		echo '</ul>';
		echo '</div>';
		
		//Load appropriate plugins for this menu bar
		
		// TODO: SCARA - Is this still needed ?
		
		//if (is_array($plugins['main_menu_logged']))
		//{
		//	foreach ($plugins['main_menu_logged'] as $this_plugin)
		//	{
		//		include (api_get_path(PLUGIN_PATH)."$this_plugin/index.php");
		//	}
		//}
		
		echo '</div>';
	}
	
	function display_create_course_link()
	{
		echo "<li><a href=\"main/create_course/add_course.php\">".get_lang("CourseCreate")."</a></li>";
	}
	function display_edit_course_list_links()
	{
		echo "<li><a href=\"main/auth/courses.php\">".get_lang("CourseManagement")."</a></li>";
	}
}
?>