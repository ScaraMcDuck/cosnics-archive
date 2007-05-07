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
		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user_id()))
		{
			$this->display_header();
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->display_header();
		echo $this->get_user_html();
		$this->display_footer();
	}
	
	function get_user_html()
	{
		$table = new SubscribedUserBrowserTable($this, null, array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id()), $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
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
}
?>