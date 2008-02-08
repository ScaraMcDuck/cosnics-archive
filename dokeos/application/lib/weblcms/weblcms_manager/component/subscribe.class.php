<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/coursecategorymenu.class.php';
require_once dirname(__FILE__).'/coursebrowser/coursebrowsertable.class.php';
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsSubscribeComponent extends WeblcmsComponent
{
	private $category;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->category = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		$course_code = $_GET[Weblcms :: PARAM_COURSE];
		$users = $_GET[Weblcms :: PARAM_USERS];
		if(!is_array($users))
		{
			$users = array($users);
		}
		if (isset($course_code))
		{
			$course = $this->retrieve_course($course_code);
			if (isset($users) && $this->get_course()->is_course_admin($this->get_user_id()))
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

				$this->redirect(null, get_lang($message), ($success ? false : true), array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $course_code, Weblcms :: PARAM_TOOL => 'user'));
			}
			else
			{
				if ($this->get_course_subscription_url($course))
				{
					$success = $this->subscribe_user_to_course($course, '5', '0', $this->get_user_id());
					$this->redirect(null, get_lang($success ? 'UserSubscribedToCourse' : 'UserNotSubscribedToCourse'), ($success ? false : true));
				}
			}
		}

		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(null, false, true, array(Weblcms :: PARAM_ACTION)), 'name' => get_lang('MyCourses'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseSubscribe'));

		$menu = $this->get_menu_html();
		$output = $this->get_course_html();

		$this->display_header($breadcrumbs, true);
		echo $menu;
		echo $output;
		$this->display_footer();
	}

	function get_course_html()
	{
		$table = new CourseBrowserTable($this, null, null, $this->get_condition());

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
			$search['title'] = get_lang('SearchResults');
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
		$search_conditions = $this->get_search_condition();

		$condition = null;
		if (isset($this->category))
		{
			$condition = new EqualityCondition(Course :: PROPERTY_CATEGORY_CODE, $this->category);

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