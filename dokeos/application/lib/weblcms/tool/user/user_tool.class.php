<?php
/**
 * $Id$
 * User tool
 * @package application.weblcms.tool
 * @subpackage user
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/user_tool_search_form.class.php';
require_once Path :: get_application_library_path().'user_details.class.php';
require_once dirname(__FILE__).'/../../weblcms_manager/component/subscribed_user_browser/subscribed_user_browser_table.class.php';
/**
 * Tool to manage users in the course.
 */
class UserTool extends Tool
{
	const USER_DETAILS = 'user_details';
	private $search_form;

	function run()
	{
		$trail = new BreadcrumbTrail();
		
//		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
//		{
//			$this->display_header($trail);
//			Display :: display_error_message(Translation :: get("NotAllowed"));
//			$this->display_footer();
//			exit;
//		}

		$user_action = $_GET[Weblcms :: PARAM_USER_ACTION];
		if(is_null($user_action))
		{
			$user_action = $_POST[Weblcms::PARAM_COMPONENT_ACTION];
		}
		
		$breadcrumb = new BreadCrumb($this->get_url(), Translation :: get($user_action == Weblcms :: ACTION_SUBSCRIBE ? 'SubscribeUsers' : 'UnsubscribeUsers'));
		$this->set_parameter(Weblcms :: PARAM_USER_ACTION,$user_action);
		$this->display_header($trail, $breadcrumb);
		$this->search_form = new UserToolSearchForm($this, $this->get_url());
		echo '<div style="clear: both;">&nbsp;</div>';
		echo $this->search_form->display();

		switch($user_action)
		{
			case UserTool::USER_DETAILS:
				$udm = UserDataManager::get_instance();
				if(isset($_GET[Weblcms::PARAM_USERS]))
				{
					$user = $udm->retrieve_user($_GET[Weblcms::PARAM_USERS]);
					$details = new UserDetails($user);
					echo $details->toHtml();
				}
				if(isset($_POST['user_id']))
				{
					foreach($_POST['user_id'] as $index => $user_id)
					{
						$user = $udm->retrieve_user($user_id);
						$details = new UserDetails($user);
						echo $details->toHtml();
					}
				}
				break;
			case Weblcms :: ACTION_SUBSCRIBE :
				if ($this->get_course()->is_course_admin($this->get_parent()->get_user()))
				{
					echo $this->get_usertool_subscribe_modification_links();
					echo $this->get_user_subscribe_html();
				}
				break;
			case Weblcms :: ACTION_UNSUBSCRIBE :
				if ($this->get_course()->is_course_admin($this->get_parent()->get_user()))
				{
					echo $this->get_usertool_unsubscribe_modification_links();
				}
				echo $this->get_user_unsubscribe_html();
				break;
			default :
				if ($this->get_course()->is_course_admin($this->get_parent()->get_user()))
				{
					echo $this->get_usertool_unsubscribe_modification_links();
				}
				echo $this->get_user_unsubscribe_html();
		}

		$this->display_footer();
	}

	function get_user_unsubscribe_html()
	{
		$table = new SubscribedUserBrowserTable($this, array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id()), $this->get_unsubscribe_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}

	function get_user_subscribe_html()
	{
		$table = new SubscribedUserBrowserTable($this, array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id()), $this->get_subscribe_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}

	function get_unsubscribe_condition()
	{
		$condition = null;

		$users = $this->get_parent()->retrieve_course_users($this->get_course());

		$conditions = array();
		while ($user = $users->next_result())
		{
			$conditions[] = new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_user());
		}

		$condition = new OrCondition($conditions);

		if ($this->search_form->get_condition())
		{
			$condition = new AndCondition($condition, $this->search_form->get_condition());
		}
		return $condition;
	}

	function get_subscribe_condition()
	{
		$condition = null;

		$users = $this->get_parent()->retrieve_course_users($this->get_course());

		$conditions = array();
		while ($user = $users->next_result())
		{
			$conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_user()));
		}

		$condition = new AndCondition($conditions);

		if ($this->search_form->get_condition())
		{
			$condition = new AndCondition($condition, $this->search_form->get_condition());
		}
		return $condition;
	}

	function get_usertool_unsubscribe_modification_links()
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->get_parent()->get_url(array(Weblcms :: PARAM_USER_ACTION => Weblcms :: ACTION_SUBSCRIBE)),
			'label' => Translation :: get('SubscribeUsers'),
			'img' => Theme :: get_common_img_path().'action_subscribe.png',
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}

	function get_usertool_subscribe_modification_links()
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->get_parent()->get_url(array(Weblcms :: PARAM_USER_ACTION => Weblcms :: ACTION_UNSUBSCRIBE)),
			'label' => Translation :: get('UnsubscribeUsers'),
			'img' => Theme :: get_common_img_path().'action_unsubscribe.png',
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>