<?php
/**
 * $Id$
 * User tool
 * @package application.weblcms.tool
 * @subpackage user
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/../../weblcms_manager/component/subscribeduserbrowser/subscribeduserbrowsertable.class.php';
/**
 * Tool to manage users in the course.
 * @todo: Implementation (recycle old user tool)
 */
class UserTool extends Tool
{
	function run()
	{
//		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user_id()))
//		{
//			$this->display_header();
//			Display :: display_error_message(get_lang("NotAllowed"));
//			$this->display_footer();
//			exit;
//		}
		
		$user_action = $_GET[Weblcms :: PARAM_USER_ACTION];
		
		$this->display_header();
		
		switch($user_action)
		{
			case Weblcms :: ACTION_SUBSCRIBE :
				if ($this->get_course()->is_course_admin($this->get_parent()->get_user_id()))
				{
					echo $this->get_usertool_subscribe_modification_links();
					echo $this->get_user_subscribe_html();
				}
				break;
			case Weblcms :: ACTION_UNSUBSCRIBE :
				if ($this->get_course()->is_course_admin($this->get_parent()->get_user_id()))
				{
					echo $this->get_usertool_unsubscribe_modification_links();
				}
				echo $this->get_user_unsubscribe_html();
				break;
			default :
				if ($this->get_course()->is_course_admin($this->get_parent()->get_user_id()))
				{
					echo $this->get_usertool_unsubscribe_modification_links();
				}
				echo $this->get_user_unsubscribe_html();
		}
		
		$this->display_footer();
	}
	
	function get_user_unsubscribe_html()
	{
		$table = new SubscribedUserBrowserTable($this, null, array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id()), $this->get_unsubscribe_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_user_subscribe_html()
	{
		$table = new SubscribedUserBrowserTable($this, null, array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id()), $this->get_subscribe_condition());
		
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
		return $condition;
	}
	
	function get_usertool_unsubscribe_modification_links()
	{
		$toolbar_data = array();
			
		$toolbar_data[] = array(
			'href' => $this->get_parent()->get_url(array(Weblcms :: PARAM_USER_ACTION => Weblcms :: ACTION_SUBSCRIBE)),
			'label' => get_lang('SubscribeUsers'),
			'img' => $this->get_parent()->get_web_code_path().'img/user-subscribe.gif',
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
	
	function get_usertool_subscribe_modification_links()
	{
		$toolbar_data = array();
			
		$toolbar_data[] = array(
			'href' => $this->get_parent()->get_url(array(Weblcms :: PARAM_USER_ACTION => Weblcms :: ACTION_UNSUBSCRIBE)),
			'label' => get_lang('UnsubscribeUsers'),
			'img' => $this->get_parent()->get_web_code_path().'img/user-unsubscribe.gif',
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>