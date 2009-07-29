<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
/**
 * Weblcms component which provides the course page
 */
class WeblcmsManagerCourseViewerComponent extends WeblcmsManagerComponent
{
	/**
	 * The tools that this application offers.
	 */
	private $tools;
	/**
	 * The class of the tool currently active in this application
	 */
	private $tool_class;

	/**
	 * The course object of the course currently active in this application
	 */
	private $course;


	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses general');

		if(!$this->is_course())
		{
			$this->display_header($trail, false, true);
			Display :: error_message(Translation :: get("NotACourse"));
			$this->display_footer();
			exit;
		}

		$this->load_course_theme();
		$this->load_course_language();

		/**
		 * Here we set the rights depending on the user status in the course.
		 * This completely ignores the roles-rights library.
		 * TODO: WORK NEEDED FOR PROPPER ROLES-RIGHTS LIBRARY
		 */

		$user = $this->get_user();
		$course = $this->get_course();
		if ($user != null && $course != null)
			$relation = $this->retrieve_course_user_relation($course->get_id(),$user->get_id());

		/*if(!$user->is_platform_admin() && (!$relation || ($relation->get_status() != 5 && $relation->get_status() != 1)))
		//TODO: Roles & Rights
		//if(!$this->is_allowed(VIEW_RIGHT) && !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail, false, true);
			Display :: not_allowed();
			$this->display_footer();
			exit;
		}*/

		$course = $this->get_parameter(WeblcmsManager :: PARAM_COURSE);
		$tool = $this->get_parameter(WeblcmsManager :: PARAM_TOOL);
		$action = $this->get_parameter(Application :: PARAM_ACTION);
		$component_action = $this->get_parameter(WeblcmsManager :: PARAM_COMPONENT_ACTION);
		$category = $this->get_parameter(WeblcmsManager :: PARAM_CATEGORY);

		if(is_null($category))
		{
			$category = 0;
		}

		if ($course)
		{
			if($component_action)
			{
				$wdm = WeblcmsDataManager :: get_instance();
				switch($component_action)
				{
					case 'make_visible':
						$wdm->set_module_visible($this->get_course_id(),$tool,true);
						$this->load_tools();
						break;
					case 'make_invisible':
						$wdm->set_module_visible($this->get_course_id(),$tool,false);
						$this->load_tools();
						break;
					case 'make_publication_invisible':
						$publication = $wdm->retrieve_learning_object_publication(Request :: get('pid'));
						$publication->set_hidden(1);
						$publication->update();
						break;
					case 'make_publication_visible':
						$publication = $wdm->retrieve_learning_object_publication(Request :: get('pid'));
						$publication->set_hidden(0);
						$publication->update();
						break;
					case 'delete_publication':
						$publication = $wdm->retrieve_learning_object_publication(Request :: get('pid'));
						$publication->set_show_on_homepage(0);
						$publication->update();
						break;
				}
				$this->set_parameter(WeblcmsManager :: PARAM_TOOL, null);
			}
			if ($tool && !$component_action)
			{
				if($tool != 'course_group')
				{
					$this->set_parameter('course_group', null);
				}
				
				$wdm = WeblcmsDataManager :: get_instance();
				$class = Tool :: type_to_class($tool);
				$toolObj = new $class ($this->get_parent());
				$this->set_tool_class($class);
				$toolObj->run();
				$wdm->log_course_module_access($this->get_course_id(),$this->get_user_id(),$tool,$category);
			}
			else
			{
				$trail = new BreadcrumbTrail();
				$this->set_parameter('pid', null);
				$this->set_parameter('tool_action', null);
				$this->set_parameter('course_group', null);
				
				switch($this->get_course()->get_breadcrumb())
				{
					case Course :: BREADCRUMB_TITLE : $title = $this->get_course()->get_name(); break;
					case Course :: BREADCRUMB_CODE : $title = $this->get_course()->get_visual(); break;
					case Course :: BREADCRUMB_COURSE_HOME : $title = Translation :: get('CourseHome'); break;
					default: $title = $this->get_course()->get_visual(); break;
				}

                if(Request :: get('previous') == 'admin')
                {
                    $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
                }
                else
                {
                    $trail->add(new Breadcrumb($this->get_url(array('go' => null, 'course' => null)), Translation :: get('MyCourses')));
                }
				$trail->add(new Breadcrumb($this->get_url(), $title));
				$trail->add_help('courses general');

				$wdm = WeblcmsDataManager :: get_instance();

				$this->display_header($trail, false, true);

				/*$tb_data = array();
				$tb_data[] = array(
					'href' => $this->get_course()->get_extlink_url(),
					'label' => $this->get_course()->get_extlink_name(),
					'icon' => Theme :: get_common_image_path().'action_home.png',
					'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);*/
				//dump($tb_data);
				echo DokeosUtilities :: build_toolbar($tb_data);

				//TODO: Depending on settings, display menu and/or shortcut icons
				//Display shortcut icons
				if($this->get_course()->get_tool_shortcut() == Course :: TOOL_SHORTCUT_ON)
				{
					$renderer = ToolListRenderer::factory('Shortcut', $this);
					echo '<div style="width: 100%; text-align: right;">';
					$renderer->display();
					echo '</div>';
				}

				echo '<div class="clear"></div>';

				//Display menu
				$menu_style = $this->get_course()->get_menu();
				if($menu_style != Course :: MENU_OFF)
				{
					$renderer = ToolListRenderer :: factory('Menu', $this);
					$renderer->display();
					echo '<div id="tool_browser_'. ($renderer->display_menu_icons() && !$renderer->display_menu_text() ? 'icon_' : '') . $renderer->get_menu_style() .'">';
				}
				else
				{
					echo '<div id="tool_browser">';
				}
				if(PlatformSetting :: get('enable_introduction', 'weblcms'))
				{
					echo $this->display_introduction_text();
				}

				echo '<div class="clear"></div>';

				$renderer = ToolListRenderer::factory('FixedLocation',$this);
				$renderer->display();
				echo '</div>';
				$this->display_footer();
				$wdm->log_course_module_access($this->get_course_id(),$this->get_user_id(),null);
			}
		}
		else
		{
			Display :: header(Translation :: get('MyCourses'), 'Mycourses');
			$this->display_footer();
		}
	}

// TODO: New Roles & Rights system
//	function is_allowed($right)
//	{
//		$user_id = $this->get_user_id();
//		$course_id = $this->get_course_id();
//		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
//		$location_id = RolesRights::get_course_location_id($course_id, TOOL_COURSE_HOMEPAGE);
//
//		$result = RolesRights::is_allowed_which_rights($role_id, $location_id);
//		return $result[$right];
//	}

	function is_course()
	{
		return ($this->get_course()->get_id() != null ? true : false);
	}

	function load_course_theme()
	{
		$course_can_have_theme = $this->get_platform_setting('allow_course_theme_selection');
		$course = $this->get_course();

		if ($course_can_have_theme && $course->has_theme())
		{
			Theme :: set_theme($course->get_theme());
		}
	}

	function load_course_language()
	{
		$course = $this->get_course();
		Translation :: set_language($course->get_language());
	}

	function display_introduction_text()
	{
		$html = array();
		
		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'introduction');
		$condition = new AndCondition($conditions);

		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications_new($condition);
		$introduction_text = $publications->next_result();
	
		if($introduction_text)
		{

			$tb_data[] = array(
				'href' => $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_EDIT_INTRODUCTION)),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);

			$tb_data[] = array(
				'href' => $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_DELETE_INTRODUCTION)),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);

			$html = array();

			$html[] = '<div class="block" id="block_introduction" style="background-image: url('.Theme :: get_image_path('home').'block_home.png);">';
			$html[] = '<div class="title"><div style="float:left;">'. $introduction_text->get_learning_object()->get_title() . '</div>';
			$html[] = '<a href="#" class="closeEl"><img class="visible" src="'.Theme :: get_common_image_path().'action_visible.png"/><img class="invisible" style="display: none;") src="'.Theme :: get_common_image_path().'action_invisible.png" /></a>';
			$html[] = '<div style="clear: both;"></div></div>';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_learning_object()->get_description();
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}
		else
		{
			if($this->is_allowed(EDIT_RIGHT))
			{
				$tb_data[] = array(
					'href' => $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_PUBLISH_INTRODUCTION)),
					'label' => Translation :: get('PublishIntroductionText'),
					'img' => Theme :: get_common_image_path() . 'action_introduce.png',
					'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
			}

			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
		}

		return implode("\n",$html);
	}
}
?>