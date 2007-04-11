<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/adminuserbrowser/adminuserbrowsertable.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class UserManagerAdminUserBrowserComponent extends UserManagerComponent
{
//	private $category;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		//$this->category = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserList'));
		
		if (!api_is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		//$menu = $this->get_menu_html();
		$output = $this->get_user_html();
		
		$this->display_header($breadcrumbs, true);
		//echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new AdminUserBrowserTable($this, null, null, $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}

	function get_condition()
	{
		//$search_conditions = $this->get_search_condition();
		
		$condition = null;
//		if (isset($this->category))
//		{
//			$condition = new EqualityCondition(User :: PROPERTY_CATEGORY_CODE, $this->category);
//			
//			if (count($search_conditions))
//			{
//				$condition = new AndCondition($condition, $search_conditions);
//			}
//		}
//		else
//		{
//			if (count($search_conditions))
//			{
//				$condition = $search_conditions;
//			}
//		}
		
		return $condition;
	}
}
?>