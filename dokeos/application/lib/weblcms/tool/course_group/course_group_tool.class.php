<?php
/**
 * $Id$
 * CourseGroup tool
 * @package application.weblcms.tool
 * @subpackage course_group
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/../../course_group/course_group_form.class.php';
require_once dirname(__FILE__).'/user_table/course_group_subscribed_user_browser_table.class.php';
require_once dirname(__FILE__).'/user_table/course_group_unsubscribed_user_browser_table.class.php';
require_once dirname(__FILE__).'/course_group_tool_search_form.class.php';
require_once dirname(__FILE__).'/course_group_table/course_group_table.class.php';
require_once dirname(__FILE__).'/course_group_table/default_course_group_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/course_group_table/default_course_group_table_column_model.class.php';
require_once dirname(__FILE__).'/course_group_table/course_group_table_data_provider.class.php';
/**
 * This tool provides an interface for managing the course_groups in a course.
 */
class CourseGroupTool extends Tool
{
	const PARAM_COURSE_GROUP_ACTION = 'course_group_action';
	const ACTION_SUBSCRIBE = 'course_group_subscribe';
	const ACTION_UNSUBSCRIBE = 'course_group_unsubscribe';
	const ACTION_ADD_COURSE_GROUP = 'add_course_group';
	const ACTION_USER_SELF_SUBSCRIBE = 'user_subscribe';
	const ACTION_USER_SELF_UNSUBSCRIBE = 'user_unsubscribe';
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
					case self :: ACTION_SUBSCRIBE :
						$html = array ();
						$this->display_header($trail);
						$html[] = '<div style="clear: both;">&nbsp;</div>';
						$html[] = $this->search_form->display();
						if ($this->get_course()->is_course_admin($this->get_parent()->get_user()))
						{
							$html[] = $this->get_course_grouptool_subscribe_modification_links();
						}
						if(isset($_GET[Weblcms::PARAM_USERS]))
						{
							$udm = UserDataManager :: get_instance();
							$user = $udm->retrieve_user($_GET[Weblcms :: PARAM_USERS]);
							$course_group = $this->get_parent()->get_course_group();
							$course_group->subscribe_users($user);
							$html[] = Display::display_normal_message(Translation :: get('UserSubscribed'),true);
						}
						$table = new CourseGroupUnsubscribedUserBrowserTable($this->get_parent(), array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id()),$this->search_form->get_condition());
						$html[] = $table->as_html();
						echo implode($html, "\n");
						$this->display_footer();
						break;
					// User self unregisters from course_group
					case self :: ACTION_USER_SELF_UNSUBSCRIBE :
						$course_group = $this->get_parent()->get_course_group();
						$course_group->unsubscribe_users($this->get_user());
						$this->display_header($trail);
						Display::display_normal_message(Translation :: get('UserUnSubscribed'));
						$this->display_footer();
						break;
					// User self registers in course_group
					case self :: ACTION_USER_SELF_SUBSCRIBE :
						$course_group = $this->get_parent()->get_course_group();
						$course_group->subscribe_users($this->get_user());
						$message = Display::display_normal_message(Translation :: get('UserSubscribed'),true);
					default :
						$course_group = $this->get_parent()->get_course_group();
						$html = array ();
						$this->display_header($trail);
						if(!is_null($message))
						{
							$html[] = $message;
						}
						$html[] = Translation :: get('Members').': '.$course_group->count_members().' / '.$course_group->get_max_number_of_members();
						$html[] = '<div style="clear: both;">&nbsp;</div>';
						$html[] = $this->search_form->display();
						if ($this->get_course()->is_course_admin($this->get_parent()->get_user()))
						{
							$html[] =  $this->get_course_grouptool_unsubscribe_modification_links();
						}
						if($course_group_action == self :: ACTION_UNSUBSCRIBE && isset($_GET[Weblcms::PARAM_USERS]))
						{
							$udm = UserDataManager :: get_instance();
							$user = $udm->retrieve_user($_GET[Weblcms :: PARAM_USERS]);
							$course_group->unsubscribe_users($user);
							$html[] = Display::display_normal_message(Translation :: get('UserUnsubscribed'),true);
						}
						$table = new CourseGroupSubscribedUserBrowserTable($this->get_parent(), array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id()),$this->search_form->get_condition());
						$html[] = $table->as_html();
						echo implode($html, "\n");
						$this->display_footer();
						break;
				}
			}
		}
		// We are outside a course_group area
		else
		{
			switch ($_GET[Tool :: PARAM_ACTION])
			{
				// Create a new course_group
				case self :: ACTION_ADD_COURSE_GROUP :
					$course_group = new CourseGroup(null, $course->get_id());
					$form = new CourseGroupForm(CourseGroupForm :: TYPE_CREATE, $course_group, $this->get_url($param_add_course_group));
					if ($form->validate())
					{
						$form->create_course_group();
						$this->get_parent()->redirect($this->get_url(), Translation :: get('CourseGroupCreated'));
					}
					else
					{
						$this->display_header($trail);
						$form->display();
						$this->display_footer();
					}
					break;
				// Display all available course_groups
				default :
					$toolbar_data[] = array ('href' => $this->get_url($param_add_course_group), 'label' => Translation :: get('Create'), 'img' => Theme :: get_common_img_path().'action_create.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
					$this->display_header($trail);
					if($this->is_allowed(EDIT_RIGHT))
					{
						echo DokeosUtilities :: build_toolbar($toolbar_data, array (), 'margin-top: 1em; margin-bottom: 1em;');
					}
					$course_group_table = new CourseGroupTable(new CourseGroupTableDataProvider($this));
					echo $course_group_table->as_html();
					$this->display_footer();
					break;
			}
		}
	}
	/**
	 * Gets the current active course_group
	 * @return CourseGroup|null The current course_group or null if no course_group is set at the
	 * moment.
	 */
	function get_course_group()
	{
		return $this->get_parent()->get_course_group();
	}
	/**
	 * Gets the toolbar to show on the page where the course_group members are listed.
	 * @return string
	 */
	function get_course_grouptool_unsubscribe_modification_links()
	{
		$toolbar_data = array ();

		$toolbar_data[] = array ('href' => $this->get_parent()->get_url(array (CourseGroupTool :: PARAM_COURSE_GROUP_ACTION => CourseGroupTool :: ACTION_SUBSCRIBE)), 'label' => Translation :: get('SubscribeUsers'), 'img' => Theme :: get_common_img_path().'action_subscribe.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
	/**
	 * Gets the toolbar to show on the page where the possible course_group members are
	 * listed.
	 * @return string
	 */
	function get_course_grouptool_subscribe_modification_links()
	{
		$toolbar_data = array ();

		$toolbar_data[] = array ('href' => $this->get_parent()->get_url(array (CourseGroupTool :: PARAM_COURSE_GROUP_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE)), 'label' => Translation :: get('UnsubscribeUsers'), 'img' => Theme :: get_common_img_path().'action_unsubscribe.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>