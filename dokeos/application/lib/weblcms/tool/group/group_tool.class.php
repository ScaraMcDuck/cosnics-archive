<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/../../group/group_form.class.php';
require_once dirname(__FILE__).'/user_table/group_subscribed_user_browser_table.class.php';
require_once dirname(__FILE__).'/user_table/group_unsubscribed_user_browser_table.class.php';
require_once dirname(__FILE__).'/group_tool_search_form.class.php';
require_once dirname(__FILE__).'/group_table/group_table.class.php';
require_once dirname(__FILE__).'/group_table/default_group_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/group_table/default_group_table_column_model.class.php';
require_once dirname(__FILE__).'/group_table/group_table_data_provider.class.php';
/**
 * This tool provides an interface for managing the groups in a course.
 */
class GroupTool extends Tool
{
	const PARAM_GROUP_ACTION = 'group_action';
	const ACTION_SUBSCRIBE = 'group_subscribe';
	const ACTION_UNSUBSCRIBE = 'group_unsubscribe';
	const ACTION_ADD_GROUP = 'add_group';
	const ACTION_USER_SELF_SUBSCRIBE = 'user_subscribe';
	const ACTION_USER_SELF_UNSUBSCRIBE = 'user_unsubscribe';
	/**
	 * The search form which can be used to search for users in the group tool.
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
		$groups = $dm->retrieve_groups($course->get_id());
		$param_add_group[RepositoryTool :: PARAM_ACTION] = self :: ACTION_ADD_GROUP;
		$this->search_form = new GroupToolSearchForm($this, $this->get_url());
		// We are inside a group area
		if (!is_null($this->get_parent()->get_group()))
		{
			$user_action = $_GET[Weblcms :: PARAM_USER_ACTION];
			$group_action = $_GET[self :: PARAM_GROUP_ACTION];
			if ($user_action == UserTool :: USER_DETAILS)
			{
				$udm = UsersDataManager :: get_instance();
				$user = $udm->retrieve_user($_GET[Weblcms :: PARAM_USERS]);
				$details = new UserDetails($user);
				$this->display_header($trail);
				echo $details->toHtml();
				$this->display_footer();
			}
			else
			{
				switch ($group_action)
				{
					case self :: ACTION_SUBSCRIBE :
						$html = array ();
						$this->display_header($trail);
						$html[] = '<div style="clear: both;">&nbsp;</div>';
						$html[] = $this->search_form->display();
						if ($this->get_course()->is_course_admin($this->get_parent()->get_user()))
						{
							$html[] = $this->get_grouptool_subscribe_modification_links();
						}
						if(isset($_GET[Weblcms::PARAM_USERS]))
						{
							$udm = UsersDataManager :: get_instance();
							$user = $udm->retrieve_user($_GET[Weblcms :: PARAM_USERS]);
							$group = $this->get_parent()->get_group();
							$group->subscribe_users($user);
							$html[] = Display::display_normal_message(Translation :: get('UserSubscribed'),true);
						}
						$table = new GroupUnsubscribedUserBrowserTable($this->get_parent(), array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id()),$this->search_form->get_condition());
						$html[] = $table->as_html();
						echo implode($html, "\n");
						$this->display_footer();
						break;
					// User self unregisters from group
					case self :: ACTION_USER_SELF_UNSUBSCRIBE :
						$group = $this->get_parent()->get_group();
						$group->unsubscribe_users($this->get_user());
						$this->display_header($trail);
						Display::display_normal_message(Translation :: get('UserUnSubscribed'));
						$this->display_footer();
						break;
					// User self registers in group
					case self :: ACTION_USER_SELF_SUBSCRIBE :
						$group = $this->get_parent()->get_group();
						$group->subscribe_users($this->get_user());
						$message = Display::display_normal_message(Translation :: get('UserSubscribed'),true);
					default :
						$group = $this->get_parent()->get_group();
						$html = array ();
						$this->display_header($trail);
						if(!is_null($message))
						{
							$html[] = $message;
						}
						$html[] = Translation :: get('Members').': '.$group->count_members().' / '.$group->get_max_number_of_members();
						$html[] = '<div style="clear: both;">&nbsp;</div>';
						$html[] = $this->search_form->display();
						if ($this->get_course()->is_course_admin($this->get_parent()->get_user()))
						{
							$html[] =  $this->get_grouptool_unsubscribe_modification_links();
						}
						if($group_action == self :: ACTION_UNSUBSCRIBE && isset($_GET[Weblcms::PARAM_USERS]))
						{
							$udm = UsersDataManager :: get_instance();
							$user = $udm->retrieve_user($_GET[Weblcms :: PARAM_USERS]);
							$group->unsubscribe_users($user);
							$html[] = Display::display_normal_message(Translation :: get('UserUnsubscribed'),true);
						}
						$table = new GroupSubscribedUserBrowserTable($this->get_parent(), array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id()),$this->search_form->get_condition());
						$html[] = $table->as_html();
						echo implode($html, "\n");
						$this->display_footer();
						break;
				}
			}
		}
		// We are outside a group area
		else
		{
			switch ($_GET[RepositoryTool :: PARAM_ACTION])
			{
				// Create a new group
				case self :: ACTION_ADD_GROUP :
					$group = new Group(null, $course->get_id());
					$form = new GroupForm(GroupForm :: TYPE_CREATE, $group, $this->get_url($param_add_group));
					if ($form->validate())
					{
						$form->create_group();
						$this->get_parent()->redirect($this->get_url(), Translation :: get('GroupCreated'));
					}
					else
					{
						$this->display_header($trail);
						$form->display();
						$this->display_footer();
					}
					break;
				// Display all available groups
				default :
					$toolbar_data[] = array ('href' => $this->get_url($param_add_group), 'label' => Translation :: get('Create'), 'img' => Theme :: get_common_img_path().'action_create.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
					$this->display_header($trail);
					if($this->is_allowed(EDIT_RIGHT))
					{
						echo DokeosUtilities :: build_toolbar($toolbar_data, array (), 'margin-top: 1em; margin-bottom: 1em;');
					}
					$group_table = new GroupTable(new GroupTableDataProvider($this));
					echo $group_table->as_html();
					$this->display_footer();
					break;
			}
		}
	}
	/**
	 * Gets the current active group
	 * @return Group|null The current group or null if no group is set at the
	 * moment.
	 */
	function get_group()
	{
		return $this->get_parent()->get_group();
	}
	/**
	 * Gets the toolbar to show on the page where the group members are listed.
	 * @return string
	 */
	function get_grouptool_unsubscribe_modification_links()
	{
		$toolbar_data = array ();

		$toolbar_data[] = array ('href' => $this->get_parent()->get_url(array (GroupTool :: PARAM_GROUP_ACTION => GroupTool :: ACTION_SUBSCRIBE)), 'label' => Translation :: get('SubscribeUsers'), 'img' => Theme :: get_common_img_path().'action_subscribe.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
	/**
	 * Gets the toolbar to show on the page where the possible group members are
	 * listed.
	 * @return string
	 */
	function get_grouptool_subscribe_modification_links()
	{
		$toolbar_data = array ();

		$toolbar_data[] = array ('href' => $this->get_parent()->get_url(array (GroupTool :: PARAM_GROUP_ACTION => GroupTool :: ACTION_UNSUBSCRIBE)), 'label' => Translation :: get('UnsubscribeUsers'), 'img' => Theme :: get_common_img_path().'action_unsubscribe.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>