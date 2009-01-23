<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_component.class.php';
require_once dirname(__FILE__).'/../../course/course_category_menu.class.php';
require_once dirname(__FILE__).'/unsubscribe_browser/unsubscribe_browser_table.class.php';
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsUnsubscribeComponent extends WeblcmsComponent
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
			if (isset($users) && $this->get_course()->is_course_admin($this->get_user()))
			{
				$failures = 0;

				foreach ($users as $user_id)
				{
					if ($user_id != $this->get_user_id())
					{
						if (!$this->unsubscribe_user_from_course($course, $user_id))
						{
							$failures++;
						}
					}
					else
					{
						$failures++;
					}
				}

				if ($failures == 0)
				{
					$success = true;

					if (count($users) == 1)
					{
						$message = 'UserUnsubscribedFromCourse';
					}
					else
					{
						$message = 'UsersUnsubscribedFromCourse';
					}
				}
				elseif ($failures == count ($users))
				{
					$success = false;

					if (count($users) == 1)
					{
						$message = 'UserNotUnsubscribedFromCourse';
					}
					else
					{
						$message = 'UsersNotUnsubscribedFromCourse';
					}
				}
				else
				{
					$success = false;
					$message = 'PartialUsersNotUnsubscribedFromCourse';
				}

				$this->redirect(null, Translation :: get($message), ($success ? false : true), array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $course_code, Weblcms :: PARAM_TOOL => 'user'));
			}
			else
			{
				if ($this->get_course_unsubscription_url($course))
				{
					$success = $this->unsubscribe_user_from_course($course, $this->get_user_id());
					$this->redirect(null, Translation :: get($success ? 'UserUnsubscribedFromCourse' : 'UserNotUnsubscribedFromCourse'), ($success ? false : true));
				}
			}
		}
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(null, false, true, array(Weblcms :: PARAM_ACTION)), Translation :: get('MyCourses')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseUnsubscribe')));		
		
		$menu = $this->get_menu_html();
		$output = $this->get_course_html();

		$this->display_header($trail);
		echo '<div class="clear"></div><br />';
		echo $menu;
		echo $output;
		$this->display_footer();
	}

	function get_course_html()
	{
		$conditions = array();
		if (isset($this->category))
		{
			$conditions[] = new EqualityCondition(Course :: PROPERTY_CATEGORY, $this->category);
		}
		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id());

		$condition = new AndCondition($conditions);

		$table = new UnsubscribeBrowserTable($this, null, $condition);

		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';

		return implode($html, "\n");
	}

	function get_menu_html()
	{
		$temp_replacement = '__CATEGORY_ID__';
		$url_format = $this->get_url(array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_MANAGER_UNSUBSCRIBE, Weblcms :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$category_menu = new CourseCategoryMenu($this->category, $url_format);

		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $category_menu->render_as_tree();
		$html[] = '</div>';

		return implode($html, "\n");
	}
}
?>