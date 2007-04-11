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
		
		$menu = $this->get_menu_html();
		$output = $this->get_user_html();
		
		$this->display_header($breadcrumbs, true);
		echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new AdminUserBrowserTable($this, null, null, $this->get_condition());
		
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
			$extra_items[] = & $search;
		}
		else
		{
			$search_url = null;
		}
		
		$url_format = $this->get_url(array (UserManager :: PARAM_ACTION => UserManager :: ACTION_ADMIN_COURSE_BROWSER));
		$user_menu = new UserMenu(null, $url_format, & $extra_items);
		
		if (isset ($search_url))
		{
			$user_menu->forceCurrentUrl($search_url, true);
		}
		
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $user_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

	function get_condition()
	{
		return $this->get_search_condition();
	}
}
?>