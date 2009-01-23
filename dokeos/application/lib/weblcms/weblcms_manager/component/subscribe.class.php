<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_component.class.php';
require_once dirname(__FILE__).'/../../course/course_category_menu.class.php';
require_once dirname(__FILE__).'/course_browser/course_browser_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsSubscribeComponent extends WeblcmsComponent
{
	private $category;
	private $action_bar;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->category = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		$course_code = $_GET[Weblcms :: PARAM_COURSE];
		$users = $_GET[Weblcms :: PARAM_USERS];
		if(isset($users) && !is_array($users))
		{
			$users = array($users);
		} 
		if (isset($course_code))
		{
			$course = $this->retrieve_course($course_code);
			if (isset($users) && count($users) > 0 && $this->get_course()->is_course_admin($this->get_user()))
			{
				$failures = 0;

				foreach ($users as $user_id)
				{
					if ($user_id != $this->get_user_id())
					{
						$status = isset($_GET[Weblcms :: PARAM_STATUS]) ? $_GET[Weblcms :: PARAM_STATUS] : 5;
						if (!$this->subscribe_user_to_course($course, $status, '0', $user_id))
						{
							$failures++;
						}
					}
				}

				if ($failures == 0)
				{
					$success = true;

					if (count($users) == 1)
					{
						$message = 'UserSubscribedToCourse';
					}
					else
					{
						$message = 'UsersSubscribedToCourse';
					}
				}
				elseif ($failures == count ($users))
				{
					$success = false;

					if (count($users) == 1)
					{
						$message = 'UserNotSubscribedToCourse';
					}
					else
					{
						$message = 'UsersNotSubscribedToCourse';
					}
				}
				else
				{
					$success = false;
					$message = 'PartialUsersNotSubscribedToCourse';
				}

				$this->redirect(null, Translation :: get($message), ($success ? false : true), array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $course_code, Weblcms :: PARAM_TOOL => 'user'));
			}
			else
			{
				if ($this->get_course_subscription_url($course))
				{
					$success = $this->subscribe_user_to_course($course, '5', '0', $this->get_user_id());
					$this->redirect(null, Translation :: get($success ? 'UserSubscribedToCourse' : 'UserNotSubscribedToCourse'), ($success ? false : true));
				}
			}
		}

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(null, false, true, array(Weblcms :: PARAM_ACTION)), Translation :: get('MyCourses')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseSubscribe')));

		$this->action_bar = $this->get_action_bar();

		$menu = $this->get_menu_html();
		$output = $this->get_course_html();

		$this->display_header($trail, false);
		echo '<div class="clear"></div>';
		echo '<br />' . $this->action_bar->as_html() . '<br />';
		echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array('category' => Request :: get('category'))));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}

	function get_course_html()
	{
		$table = new CourseBrowserTable($this, null, $this->get_condition());

		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';

		return implode($html, "\n");
	}

	function get_menu_html()
	{
		$extra_items = array ();
		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = $search;
		}
		else
		{
			$search_url = null;
		}

		$temp_replacement = '__CATEGORY_ID__';
		$url_format = $this->get_url(array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_MANAGER_SUBSCRIBE, Weblcms :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$category_menu = new CourseCategoryMenu($this->category, $url_format);

		if (isset ($search_url))
		{
			$category_menu->forceCurrentUrl($search_url, true);
		}

		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $category_menu->render_as_tree();
		$html[] = '</div>';

		return implode($html, "\n");
	}

	function get_condition()
	{
		$query = $this->action_bar->get_query();
		
		if (isset($query) && $query != '')
		{
			$conditions = array ();
			$conditions[] = new PatternMatchCondition(Course :: PROPERTY_ID, '*'.$query.'*');
			$conditions[] = new PatternMatchCondition(Course :: PROPERTY_NAME, '*'.$query.'*');
			$conditions[] = new PatternMatchCondition(Course :: PROPERTY_LANGUAGE, '*'.$query.'*');
		
			$search_conditions = new OrCondition($conditions);
		}

		$condition = null;
		if (isset($this->category))
		{
			$condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $this->category);

			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}

		return $condition;
	}
}
?>